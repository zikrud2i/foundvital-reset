<?php
/**
 * Theme supports, menus, image sizes, small front-end clean-ups.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'fvr', FVR_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 360, 'flex-width' => true, 'flex-height' => true ) );

		register_nav_menus(
			array(
				'primary' => __( 'Primary navigation', 'fvr' ),
				'footer'  => __( 'Footer navigation', 'fvr' ),
				'legal'   => __( 'Legal links', 'fvr' ),
			)
		);

		add_image_size( 'fvr-portrait', 820, 1025, true );
		add_image_size( 'fvr-wide', 1280, 720, true );
	}
);

/** Remove emoji and embed scripts we do not need. */
add_action(
	'init',
	static function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
	}
);

/** Body classes: page slug + "has-js" is added in header via inline script. */
add_filter(
	'body_class',
	static function ( $classes ) {
		if ( is_page() ) {
			$classes[] = 'page-' . get_post_field( 'post_name', get_the_ID() );
		}
		return $classes;
	}
);

/** Fallback nav used until the client assigns a menu to "Primary navigation". */
function fvr_default_nav(): void {
	$home  = trailingslashit( home_url() );
	$items = array(
		array( __( 'How Reset works', 'fvr' ), $home . '#how-reset-works' ),
		array( __( 'Meet Tara', 'fvr' ), $home . '#meet-tara' ),
	);
	echo '<ul class="nav__list">';
	foreach ( $items as $item ) {
		echo '<li><a href="' . esc_url( $item[1] ) . '">' . esc_html( $item[0] ) . '</a></li>';
	}
	echo '</ul>';
}

/** Breadcrumbs: Rank Math's when available, otherwise a minimal trail for nested pages. */
function fvr_breadcrumbs(): void {
	if ( ! is_page() || is_front_page() ) {
		return;
	}
	if ( function_exists( 'rank_math_the_breadcrumbs' ) && class_exists( 'RankMath\\Helper' ) && \RankMath\Helper::get_settings( 'general.breadcrumbs' ) ) {
		echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'fvr' ) . '">';
		rank_math_the_breadcrumbs();
		echo '</nav>';
		return;
	}
	$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
	if ( ! $ancestors ) {
		return;
	}
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'fvr' ) . '"><ol>';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Reset', 'fvr' ) . '</a></li>';
	foreach ( $ancestors as $anc ) {
		echo '<li><a href="' . esc_url( get_permalink( $anc ) ) . '">' . esc_html( get_the_title( $anc ) ) . '</a></li>';
	}
	echo '<li aria-current="page">' . esc_html( get_the_title() ) . '</li></ol></nav>';
}
