<?php
/**
 * Front-end and admin assets.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Asset version = theme version + file modification time, so browsers always fetch changed files. */
function fvr_asset_ver( string $rel ): string {
	$file = FVR_DIR . '/' . $rel;
	return FVR_VERSION . '.' . ( file_exists( $file ) ? (string) filemtime( $file ) : '0' );
}

/** True when any tracking ID is configured (drives the consent banner). */
function fvr_tracking_enabled(): bool {
	return (bool) ( fvr_opt( 'gtm_id' ) || fvr_opt( 'ga4_id' ) || fvr_opt( 'meta_pixel_id' ) );
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'fvr-fonts',
			'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap',
			array(),
			null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- external font URL.
		);
		wp_enqueue_style( 'fvr-main', FVR_URI . '/assets/css/main.css', array( 'fvr-fonts' ), fvr_asset_ver( 'assets/css/main.css' ) );

		wp_enqueue_script( 'fvr-main', FVR_URI . '/assets/js/main.js', array(), fvr_asset_ver( 'assets/js/main.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_localize_script(
			'fvr-main',
			'FVR',
			array(
				'tracking'     => fvr_tracking_enabled(),
				'gtm'          => (string) fvr_opt( 'gtm_id' ),
				'ga4'          => (string) fvr_opt( 'ga4_id' ),
				'pixel'        => (string) fvr_opt( 'meta_pixel_id' ),
				'fitThanksUrl' => fvr_thanks_url( 'fit-call' ),
				'calendlyOn'   => (bool) fvr_opt( 'calendly_redirect_on_book' ),
			)
		);

		if ( is_page_template( 'page-templates/quiz.php' ) ) {
			wp_enqueue_script( 'fvr-quiz', FVR_URI . '/assets/js/quiz.js', array(), fvr_asset_ver( 'assets/js/quiz.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		}

		if ( fvr_opt( 'turnstile_site_key' ) && ( is_page_template( 'page-templates/checklist.php' ) || is_page_template( 'page-templates/masterclass.php' ) || is_page_template( 'page-templates/quiz.php' ) ) ) {
			wp_enqueue_script( 'fvr-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, array( 'in_footer' => true, 'strategy' => 'defer' ) ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		}
	}
);

/** Preconnect for Google Fonts. */
add_filter(
	'wp_resource_hints',
	static function ( $urls, $relation ) {
		if ( 'preconnect' === $relation ) {
			$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
		}
		return $urls;
	},
	10,
	2
);

/** Admin: media picker + small styles for the settings page and quiz meta box. */
add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		if ( 'toplevel_page_fvr-settings' === $hook ) {
			wp_enqueue_media();
			wp_enqueue_script( 'fvr-admin', FVR_URI . '/assets/js/admin.js', array( 'jquery' ), FVR_VERSION, true );
			wp_enqueue_style( 'fvr-admin', FVR_URI . '/assets/css/admin.css', array(), FVR_VERSION );
		}
	}
);
