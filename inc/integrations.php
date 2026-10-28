<?php
/**
 * Kit (formerly ConvertKit) integration – server-side only, API v4.
 *
 * NOTE: written against Kit's documented v4 endpoints; it has NOT been run against a live
 * Kit account. Verify with a test subscriber before launch.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** API key from wp-config constant (preferred) or the settings screen. Never output to the front end. */
function fvr_kit_api_key(): string {
	if ( defined( 'FVR_KIT_API_KEY' ) && FVR_KIT_API_KEY ) {
		return (string) FVR_KIT_API_KEY;
	}
	return (string) fvr_opt( 'kit_api_key' );
}

/**
 * Low-level Kit request.
 *
 * @return array|WP_Error Decoded JSON body on 2xx.
 */
function fvr_kit_request( string $path, array $body ) {
	$key = fvr_kit_api_key();
	if ( '' === $key ) {
		return new WP_Error( 'kit_unconfigured', 'Kit API key missing.' );
	}
	$res = wp_remote_post(
		'https://api.kit.com/v4' . $path,
		array(
			'timeout' => 12,
			'headers' => array(
				'X-Kit-Api-Key' => $key,
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
			),
			'body'    => wp_json_encode( $body ),
		)
	);
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	$code = (int) wp_remote_retrieve_response_code( $res );
	if ( $code < 200 || $code >= 300 ) {
		// Log status only – never log the email address or the key.
		error_log( sprintf( '[fvr] Kit API %s returned HTTP %d', $path, $code ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		return new WP_Error( 'kit_http', 'Kit responded with HTTP ' . $code );
	}
	$json = json_decode( wp_remote_retrieve_body( $res ), true );
	return is_array( $json ) ? $json : array();
}

/**
 * Subscribe an email to Kit, optionally adding it to a form and tags.
 *
 * @param string $email   Verified email.
 * @param string $first   Optional first name.
 * @param int    $form_id Kit form ID (0 = skip).
 * @param int[]  $tag_ids Kit tag IDs.
 * @return true|WP_Error
 */
function fvr_kit_subscribe( string $email, string $first = '', int $form_id = 0, array $tag_ids = array() ) {
	$sub = array( 'email_address' => $email );
	if ( '' !== $first ) {
		$sub['first_name'] = $first;
	}
	$r = fvr_kit_request( '/subscribers', $sub ); // Upsert: creates or updates the subscriber.
	if ( is_wp_error( $r ) ) {
		return $r;
	}
	if ( $form_id > 0 ) {
		$r = fvr_kit_request( '/forms/' . $form_id . '/subscribers', array( 'email_address' => $email ) );
		if ( is_wp_error( $r ) ) {
			return $r;
		}
	}
	foreach ( array_filter( array_map( 'absint', $tag_ids ) ) as $tag_id ) {
		$r = fvr_kit_request( '/tags/' . $tag_id . '/subscribers', array( 'email_address' => $email ) );
		if ( is_wp_error( $r ) ) {
			return $r;
		}
	}
	return true;
}
