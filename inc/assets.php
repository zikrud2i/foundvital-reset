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

/**
 * Meta Pixel: home page and /checklist/ ONLY (never quiz, results, booking, legal, client or payment pages).
 * PageView here; the single "Lead" (no parameters) fires in main.js after a confirmed checklist sign-up.
 * autoConfig is off so Meta never reads buttons or form fields on its own.
 */
function fvr_pixel_page(): bool {
	return is_front_page() || ( is_page() && 'checklist' === get_page_uri( get_queried_object_id() ) );
}

add_action(
	'wp_head',
	static function () {
		$id = preg_replace( '/\D+/', '', (string) fvr_opt( 'meta_pixel_id' ) );
		if ( ! $id || ! fvr_pixel_page() ) {
			return;
		}
		?>
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('set','autoConfig',false,'<?php echo esc_js( $id ); ?>');
fbq('init','<?php echo esc_js( $id ); ?>');
fbq('track','PageView');
</script>
<noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id=<?php echo esc_attr( $id ); ?>&amp;ev=PageView&amp;noscript=1"></noscript>
		<?php
	},
	20
);
