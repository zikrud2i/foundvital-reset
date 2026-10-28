<?php
/**
 * Inner-page header. Args: eyebrow, headline, intro (all optional, fall back to page fields).
 *
 * @package foundvital-reset
 */

$args     = isset( $args ) && is_array( $args ) ? $args : array();
$eyebrow  = $args['eyebrow'] ?? fvr_field( 'page_eyebrow', '' );
$headline = fvr_field( 'page_headline', '' ) ? fvr_field( 'page_headline', '' ) : ( $args['headline'] ?? get_the_title() );
$intro    = fvr_field( 'page_intro', '' ) ? fvr_field( 'page_intro', '' ) : ( $args['intro'] ?? '' );
?>
<section class="page-hero">
	<div class="container container--narrow text-center">
		<?php fvr_breadcrumbs(); ?>
		<?php if ( $eyebrow ) : ?>
			<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<h1><?php echo esc_html( $headline ); ?></h1>
		<?php if ( $intro ) : ?>
			<p class="lead"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
	</div>
</section>
