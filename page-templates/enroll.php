<?php
/**
 * Template Name: Reset – Enrollment (Stripe hosted checkout)
 * For people who have had a fit conversation. Does not collect payment details itself.
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$stripe = (string) fvr_opt( 'stripe_reset_url' );
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow'  => 'Enrollment',
			'headline' => 'Enroll in FoundVital Reset',
			'intro'    => 'This page is for people who have already had a fit conversation and would like to move forward.',
		)
	);
	?>
	<section class="section section--tight">
		<div class="container container--narrow">
			<?php get_template_part( 'template-parts/pricing' ); ?>

			<div class="panel">
				<?php if ( $stripe ) : ?>
					<h2 class="h3"><?php esc_html_e( 'Ready to enroll?', 'fvr' ); ?></h2>
					<ul class="check-list check-list--small">
						<li><?php echo fvr_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Payment is taken on Stripe\'s secure, hosted checkout page. This website never sees or stores your card details.', 'fvr' ); ?></span></li>
						<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Selecting the button below only takes you to checkout. Your enrollment is not complete until you finish payment there.', 'fvr' ); ?></span></li>
					</ul>
					<div class="btn-row"><?php fvr_btn( __( 'Continue to Secure Checkout', 'fvr' ), $stripe, 'primary', 'stripe_checkout_click_reset' ); ?></div>
				<?php else : ?>
					<h2 class="h3"><?php esc_html_e( 'Enrollment isn\'t open on this page yet.', 'fvr' ); ?></h2>
					<p><?php esc_html_e( 'If you\'d like to talk about Reset first, a fit conversation is the best place to start.', 'fvr' ); ?></p>
					<div class="btn-row"><?php fvr_fit_btn(); ?></div>
					<?php fvr_pending( 'Add the approved Stripe Payment Link for Reset (Reset Settings > Links & checkout) once price and billing terms are confirmed.' ); ?>
				<?php endif; ?>
			</div>
			<p class="fineprint"><?php esc_html_e( 'Reset is coaching and education. Lab testing, medication, and other clinical services are separate.', 'fvr' ); ?></p>
		</div>
	</section>
	<?php
endwhile;
get_footer();
