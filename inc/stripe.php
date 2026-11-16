<?php
/**
 * Stripe: end the 3-payment plan after exactly three payments.
 *
 * Stripe has no "stop after N payments" setting on a plain subscription. This file adds a webhook endpoint:
 * when a subscription starts on the plan's monthly price, it is converted to a subscription schedule with one
 * phase lasting 3 months and end behaviour "cancel". The client is charged 3 times and the subscription
 * then ends on its own (no proration).
 *
 * Endpoint:  POST {site}/wp-json/fvr/v1/stripe-webhook   (Stripe event: customer.subscription.created)
 * Secrets:   FVR_STRIPE_WEBHOOK_SECRET and FVR_STRIPE_API_KEY in wp-config.php (preferred), or the Settings screen.
 * Key scope: a restricted key with Subscriptions: Write is enough. The same code serves test mode and live mode,
 *            so use the test-mode key and secret first, then swap in the live ones.
 * No card data, names or emails are read or stored; the log keeps only Stripe object IDs.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const FVR_STRIPE_PLAN_PAYMENTS = 3;

/** Pinned so the request format below keeps working if the Stripe account's default API version changes. */
const FVR_STRIPE_API_VERSION = '2026-01-28.clover';

function fvr_stripe_api_key(): string {
	return ( defined( 'FVR_STRIPE_API_KEY' ) && FVR_STRIPE_API_KEY ) ? (string) FVR_STRIPE_API_KEY : (string) fvr_opt( 'stripe_api_key' );
}

function fvr_stripe_webhook_secret(): string {
	return ( defined( 'FVR_STRIPE_WEBHOOK_SECRET' ) && FVR_STRIPE_WEBHOOK_SECRET ) ? (string) FVR_STRIPE_WEBHOOK_SECRET : (string) fvr_opt( 'stripe_webhook_secret' );
}

function fvr_stripe_plan_price(): string {
	return trim( (string) fvr_opt( 'stripe_plan_price', 'price_1UWEcjKhOmg4nXw90Fjg8XVD' ) );
}

function fvr_stripe_webhook_url(): string {
	return rest_url( 'fvr/v1/stripe-webhook' );
}

/** Verify the Stripe-Signature header (t=timestamp,v1=hmac) against the raw body. */
function fvr_stripe_verify_signature( string $payload, string $header, string $secret, int $tolerance = 300 ): bool {
	if ( '' === $secret || '' === $header ) {
		return false;
	}
	$ts   = 0;
	$sigs = array();
	foreach ( explode( ',', $header ) as $part ) {
		$kv = explode( '=', trim( $part ), 2 );
		if ( 2 !== count( $kv ) ) {
			continue;
		}
		if ( 't' === $kv[0] ) {
			$ts = (int) $kv[1];
		} elseif ( 'v1' === $kv[0] ) {
			$sigs[] = $kv[1];
		}
	}
	if ( ! $ts || ! $sigs || abs( time() - $ts ) > $tolerance ) {
		return false;
	}
	$expected = hash_hmac( 'sha256', $ts . '.' . $payload, $secret );
	foreach ( $sigs as $sig ) {
		if ( hash_equals( $expected, $sig ) ) {
			return true;
		}
	}
	return false;
}

/** Minimal Stripe REST call (form-encoded). Returns the decoded JSON or WP_Error. */
function fvr_stripe_request( string $method, string $path, array $body = array(), string $idempotency = '' ) {
	$key = fvr_stripe_api_key();
	if ( ! $key ) {
		return new WP_Error( 'fvr_stripe_nokey', 'No Stripe API key configured.' );
	}
	$url  = 'https://api.stripe.com/v1/' . ltrim( $path, '/' );
	$args = array(
		'method'  => $method,
		'timeout' => 20,
		'headers' => array(
			'Authorization'  => 'Bearer ' . $key,
			'Stripe-Version' => FVR_STRIPE_API_VERSION,
		),
	);
	if ( $idempotency ) {
		$args['headers']['Idempotency-Key'] = $idempotency;
	}
	if ( 'GET' === $method ) {
		if ( $body ) {
			$url .= '?' . http_build_query( $body );
		}
	} else {
		$args['body'] = $body;
	}
	$res = wp_remote_request( $url, $args );
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	$code = (int) wp_remote_retrieve_response_code( $res );
	$data = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( $code < 200 || $code >= 300 || ! is_array( $data ) ) {
		$msg = is_array( $data ) && isset( $data['error']['message'] ) ? (string) $data['error']['message'] : 'HTTP ' . $code;
		return new WP_Error( 'fvr_stripe_api', $msg, array( 'status' => $code ) );
	}
	return $data;
}

/** Keep the last 25 outcomes (Stripe IDs and a result word only) so a failed run can be diagnosed. */
function fvr_stripe_log( string $event_id, string $sub_id, string $result, string $schedule = '' ): void {
	$log   = get_option( 'fvr_stripe_log', array() );
	$log   = is_array( $log ) ? $log : array();
	$log[] = array(
		'time'     => gmdate( 'c' ),
		'event'    => $event_id,
		'sub'      => $sub_id,
		'schedule' => $schedule,
		'result'   => $result,
	);
	update_option( 'fvr_stripe_log', array_slice( $log, -25 ), false );
}

/** True when this subscription includes the 3-payment plan's price. */
function fvr_stripe_sub_on_plan( array $sub ): bool {
	$plan = fvr_stripe_plan_price();
	foreach ( $sub['items']['data'] ?? array() as $item ) {
		if ( ( $item['price']['id'] ?? '' ) === $plan ) {
			return true;
		}
	}
	return false;
}

/**
 * Convert the subscription to a 3-month schedule that cancels itself.
 *
 * @return array{0:int,1:string} HTTP status to return to Stripe (5xx makes Stripe retry) and a result word.
 */
function fvr_stripe_apply_plan( array $event ): array {
	$eid     = (string) ( $event['id'] ?? '' );
	$payload = $event['data']['object'] ?? array();
	$sub_id  = (string) ( $payload['id'] ?? '' );
	if ( ! $sub_id || 0 !== strpos( $sub_id, 'sub_' ) ) {
		return array( 200, 'ignored_no_subscription' );
	}
	if ( ! fvr_stripe_sub_on_plan( $payload ) ) {
		return array( 200, 'ignored_other_price' );
	}

	// Re-read the subscription so a retry never trusts a stale payload.
	$sub = fvr_stripe_request( 'GET', 'subscriptions/' . $sub_id );
	if ( is_wp_error( $sub ) ) {
		fvr_stripe_log( $eid, $sub_id, 'error_read: ' . $sub->get_error_message() );
		return array( 500, 'error' );
	}
	if ( ! fvr_stripe_sub_on_plan( $sub ) || in_array( $sub['status'] ?? '', array( 'canceled', 'incomplete_expired' ), true ) ) {
		fvr_stripe_log( $eid, $sub_id, 'skipped_status_' . ( $sub['status'] ?? '' ) );
		return array( 200, 'skipped' );
	}

	// Use the existing schedule (a previous attempt may have created one) or create it from the subscription.
	if ( ! empty( $sub['schedule'] ) ) {
		$schedule = fvr_stripe_request( 'GET', 'subscription_schedules/' . $sub['schedule'] );
	} else {
		$schedule = fvr_stripe_request( 'POST', 'subscription_schedules', array( 'from_subscription' => $sub_id ) );
	}
	if ( is_wp_error( $schedule ) ) {
		fvr_stripe_log( $eid, $sub_id, 'error_schedule: ' . $schedule->get_error_message() );
		return array( 500, 'error' );
	}
	$sid = (string) ( $schedule['id'] ?? '' );
	// A schedule made from a subscription starts as "release"; "cancel" is only ever set by the update below.
	if ( 'cancel' === ( $schedule['end_behavior'] ?? '' ) ) {
		fvr_stripe_log( $eid, $sub_id, 'already_configured', $sid );
		return array( 200, 'already_configured' );
	}

	$phase = $schedule['phases'][0] ?? array();
	$items = array();
	foreach ( $phase['items'] ?? array() as $it ) {
		$row = array(
			'price'    => is_array( $it['price'] ?? null ) ? $it['price']['id'] : (string) ( $it['price'] ?? '' ),
			'quantity' => (int) ( $it['quantity'] ?? 1 ),
		);
		if ( ! empty( $it['tax_rates'] ) ) {
			$row['tax_rates'] = array_map( static fn( $t ) => is_array( $t ) ? $t['id'] : $t, $it['tax_rates'] );
		}
		$items[] = $row;
	}
	if ( ! $sid || ! $items || empty( $phase['start_date'] ) ) {
		fvr_stripe_log( $eid, $sub_id, 'error_unexpected_schedule_shape', $sid );
		return array( 500, 'error' );
	}

	$new_phase = array(
		'start_date'         => (int) $phase['start_date'],
		'items'              => $items,
		'duration'           => array(
			'interval'       => 'month',
			'interval_count' => FVR_STRIPE_PLAN_PAYMENTS,
		),
		'proration_behavior' => 'none',
	);
	// Stripe clears anything left out of a phase update. Losing the saved card would fail payments 2 and 3.
	if ( ! empty( $phase['default_payment_method'] ) ) {
		$new_phase['default_payment_method'] = is_array( $phase['default_payment_method'] ) ? $phase['default_payment_method']['id'] : $phase['default_payment_method'];
	}
	if ( ! empty( $phase['collection_method'] ) ) {
		$new_phase['collection_method'] = $phase['collection_method'];
	}
	if ( ! empty( $phase['automatic_tax']['enabled'] ) ) {
		$new_phase['automatic_tax'] = array( 'enabled' => 'true' );
	}
	if ( ! empty( $phase['default_tax_rates'] ) ) {
		$new_phase['default_tax_rates'] = array_map( static fn( $t ) => is_array( $t ) ? $t['id'] : $t, $phase['default_tax_rates'] );
	}
	if ( ! empty( $phase['metadata'] ) ) {
		$new_phase['metadata'] = $phase['metadata'];
	}
	$discounts = array();
	foreach ( $phase['discounts'] ?? array() as $d ) {
		if ( ! empty( $d['coupon'] ) ) {
			$discounts[] = array( 'coupon' => is_array( $d['coupon'] ) ? $d['coupon']['id'] : $d['coupon'] );
		} elseif ( ! empty( $d['promotion_code'] ) ) {
			$discounts[] = array( 'promotion_code' => is_array( $d['promotion_code'] ) ? $d['promotion_code']['id'] : $d['promotion_code'] );
		}
	}
	if ( $discounts ) {
		$new_phase['discounts'] = $discounts;
	}

	$upd = fvr_stripe_request(
		'POST',
		'subscription_schedules/' . $sid,
		array(
			'end_behavior'       => 'cancel',
			'proration_behavior' => 'none',
			'phases'             => array( $new_phase ),
		)
	);
	if ( is_wp_error( $upd ) ) {
		fvr_stripe_log( $eid, $sub_id, 'error_update: ' . $upd->get_error_message(), $sid );
		return array( 500, 'error' );
	}
	fvr_stripe_log( $eid, $sub_id, 'scheduled_3_payments', $sid );
	return array( 200, 'scheduled' );
}

add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'fvr/v1',
			'/stripe-webhook',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true', // Authenticated by the Stripe signature below.
				'callback'            => static function ( WP_REST_Request $request ) {
					$raw = $request->get_body();
					if ( ! fvr_stripe_verify_signature( $raw, (string) $request->get_header( 'stripe_signature' ), fvr_stripe_webhook_secret() ) ) {
						return new WP_REST_Response( array( 'error' => 'invalid_signature' ), 400 );
					}
					$event = json_decode( $raw, true );
					if ( ! is_array( $event ) || 'customer.subscription.created' !== ( $event['type'] ?? '' ) ) {
						return new WP_REST_Response( array( 'received' => true, 'handled' => false ), 200 );
					}
					list( $status, $result ) = fvr_stripe_apply_plan( $event );
					return new WP_REST_Response( array( 'received' => true, 'result' => $result ), $status );
				},
			)
		);
	}
);
