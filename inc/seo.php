<?php
/**
 * SEO glue. Rank Math owns titles, descriptions, schema, canonicals, OG and sitemaps.
 * This file only provides a minimal description fallback when Rank Math is not active.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_head',
	static function () {
		if ( class_exists( 'RankMath' ) || ! is_singular() ) {
			return;
		}
		$desc = (string) get_post_meta( get_the_ID(), 'page_intro', true );
		if ( ! $desc ) {
			$desc = has_excerpt() ? get_the_excerpt() : '';
		}
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( wp_trim_words( wp_strip_all_tags( $desc ), 32, '…' ) ) . '">' . "\n";
		}
	},
	1
);
