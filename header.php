<?php
/**
 * Site header.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>document.documentElement.classList.add('js');</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'fvr' ); ?></a>

<div class="topbar" role="region" aria-label="<?php esc_attr_e( 'FoundVital site notice', 'fvr' ); ?>">
	<div class="container topbar__inner">
		<span class="topbar__note"><?php esc_html_e( 'Coaching and education.', 'fvr' ); ?></span>
	</div>
</div>

<header class="site-header" id="top">
	<div class="container site-header__inner">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'FoundVital Reset home', 'fvr' ); ?>">
			<?php echo fvr_logo(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="site-logo__tag">Reset<sup>™</sup></span>
		</a>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'fvr' ); ?></span>
			<span class="nav-toggle__bars" aria-hidden="true"></span>
		</button>

		<nav class="nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'fvr' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav__list',
					'fallback_cb'    => 'fvr_default_nav',
					'depth'          => 1,
				)
			);
			?>
			<div class="nav__cta"><?php fvr_fit_btn( 'primary', __( 'Book a Reset Fit Conversation', 'fvr' ), 'cta_fit_call_header' ); ?></div>
		</nav>
	</div>
</header>

<main id="main" tabindex="-1">
