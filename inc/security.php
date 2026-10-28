<?php
/**
 * Security headers, spam-protection primitives, noindex handling.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'send_headers',
	static function () {
		if ( is_admin() ) {
			return;
		}
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
	}
);

/** Signed timestamp token used as a "time to fill" bot check that survives page caching. */
function fvr_form_token(): string {
	$ts = (string) time();
	return $ts . '.' . hash_hmac( 'sha256', $ts, wp_salt( 'nonce' ) );
}

function fvr_form_token_valid( string $token, int $min = 3, int $max = DAY_IN_SECONDS ): bool {
	$parts = explode( '.', $token );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( ! hash_equals( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), $parts[1] ) ) {
		return false;
	}
	$age = time() - (int) $parts[0];
	return $age >= $min && $age <= $max;
}

function fvr_client_ip(): string {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

/** Simple per-IP rate limit using transients. Returns true when over the limit. */
function fvr_rate_limited( string $bucket, int $limit = 8, int $window = 900 ): bool {
	$key = 'fvr_rl_' . md5( $bucket . '|' . fvr_client_ip() );
	$n   = (int) get_transient( $key );
	if ( $n >= $limit ) {
		return true;
	}
	set_transient( $key, $n + 1, $window );
	return false;
}

/** Hidden anti-spam inputs (honeypot + signed timestamp + optional Turnstile). */
function fvr_spam_fields(): void {
	echo '<div class="hp-field" aria-hidden="true"><label>Leave this field empty<input type="text" name="fvr_website" tabindex="-1" autocomplete="off" value=""></label></div>';
	echo '<input type="hidden" name="fvr_token" value="' . esc_attr( fvr_form_token() ) . '">';
	$site_key = (string) fvr_opt( 'turnstile_site_key' );
	if ( $site_key ) {
		echo '<div class="cf-turnstile" data-sitekey="' . esc_attr( $site_key ) . '" data-theme="light"></div>';
	}
}

/**
 * Verify the spam fields. Returns '' when OK, or an error code.
 */
function fvr_spam_check( string $bucket ): string {
	if ( ! empty( $_POST['fvr_website'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- caller verifies nonce.
		return 'spam';
	}
	$token = isset( $_POST['fvr_token'] ) ? sanitize_text_field( wp_unslash( $_POST['fvr_token'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! fvr_form_token_valid( $token ) ) {
		return 'spam';
	}
	if ( fvr_rate_limited( $bucket ) ) {
		return 'rate';
	}
	$secret = (string) fvr_opt( 'turnstile_secret' );
	if ( $secret ) {
		$resp = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( '' === $resp ) {
			return 'captcha';
		}
		$r = wp_remote_post(
			'https://challenges.cloudflare.com/turnstile/v0/siteverify',
			array(
				'timeout' => 8,
				'body'    => array(
					'secret'   => $secret,
					'response' => $resp,
					'remoteip' => fvr_client_ip(),
				),
			)
		);
		$body = is_wp_error( $r ) ? array() : json_decode( wp_remote_retrieve_body( $r ), true );
		if ( empty( $body['success'] ) ) {
			return 'captcha';
		}
	}
	return '';
}

/** Pages that should never be indexed (confirmation, personalised, enrolment). */
function fvr_is_noindex_page(): bool {
	if ( ! is_page() ) {
		return is_404() || is_search();
	}
	return is_page_template( array( 'page-templates/thank-you.php', 'page-templates/quiz-result.php', 'page-templates/enroll.php' ) );
}

add_filter(
	'wp_robots',
	static function ( $robots ) {
		if ( fvr_is_noindex_page() ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
			unset( $robots['max-image-preview'] );
		}
		return $robots;
	}
);

/** Rank Math: keep its robots meta consistent with ours. */
add_filter(
	'rank_math/frontend/robots',
	static function ( $robots ) {
		if ( fvr_is_noindex_page() ) {
			$robots['index']  = 'noindex';
			$robots['follow'] = 'nofollow';
		}
		return $robots;
	}
);

/** Small first-party "success" cookie so thank-you pages only fire conversion events after a real success. */
function fvr_set_success_cookie( string $name ): void {
	setcookie( 'fvr_ok_' . $name, '1', array( 'expires' => time() + 900, 'path' => COOKIEPATH ? COOKIEPATH : '/', 'secure' => is_ssl(), 'samesite' => 'Lax' ) );
}
