<?php
/**
 * Template Name: Reset – Free Checklist
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$benefits = fvr_lines( fvr_field( 'page_points', "Clear priorities across nutrition, strength, recovery, and everyday habits\nA practical starting point you can use on your own\nSimple ways to decide what to focus on first" ) );
	$inside   = fvr_lines( fvr_field( 'page_extra', '' ) );
	?>
	<section class="page-hero page-hero--split">
		<div class="container split">
			<div class="split__copy">
				<?php fvr_breadcrumbs(); ?>
				<p class="eyebrow"><?php echo esc_html( fvr_field( 'page_eyebrow', 'Free resource' ) ); ?></p>
				<h1><?php echo esc_html( fvr_field( 'page_headline', 'The Midlife Health Reset Checklist' ) ); ?></h1>
				<p class="lead"><?php echo esc_html( fvr_field( 'page_intro', 'A free, practical checklist to help you see where to focus first as you build a routine that fits your real life.' ) ); ?></p>
				<ul class="check-list">
					<?php foreach ( $benefits as $b ) : ?>
						<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $b ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="split__form">
				<div class="form-card">
					<h2 class="form-card__title"><?php esc_html_e( 'Send me the checklist', 'fvr' ); ?></h2>
					<?php fvr_lead_form( 'checklist', __( 'Get the Free Checklist', 'fvr' ) ); ?>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--tight">
		<div class="container container--narrow">
			<div class="panel">
				<h2 class="h3"><?php echo esc_html( fvr_field( 'page_extra_title', 'What\'s inside' ) ); ?></h2>
				<?php if ( $inside ) : ?>
					<ul class="check-list">
						<?php foreach ( $inside as $i ) : ?>
							<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $i ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p class="muted"><?php esc_html_e( 'A real preview of the checklist will appear here.', 'fvr' ); ?></p>
					<?php fvr_pending( 'Add 3–6 real checklist items (page field "Extra section text") so the preview reflects the actual PDF.' ); ?>
				<?php endif; ?>
			</div>
			<p class="fineprint"><?php esc_html_e( 'This checklist is educational and is not medical advice.', 'fvr' ); ?></p>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/final-cta' );
endwhile;
get_footer();
