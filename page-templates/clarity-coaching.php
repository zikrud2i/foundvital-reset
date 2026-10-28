<?php
/**
 * Template Name: Reset – Clarity Coaching Session (lab + coaching pathway)
 *
 * Two SEPARATE purchases: testing via the OutfitMD affiliate shop (external), and optional
 * FoundVital coaching via FoundVital's own Stripe checkout. Never bundled into one transaction.
 * Location (this site vs. foundvital.com) is a pending architecture decision; this template can
 * be deployed on either site. Physician-review wording requires client confirmation.
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$test_price  = (string) fvr_opt( 'clarity_test_price' );
	$coach_price = (string) fvr_opt( 'clarity_coach_price' );
	$total       = (string) fvr_opt( 'clarity_total_price' );
	$shop        = (string) fvr_opt( 'outfitmd_url' );
	$stripe      = (string) fvr_opt( 'stripe_clarity_url' );
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow'  => 'FoundVital Clarity Coaching Session',
			'headline' => 'Make sense of your lab results with a coaching session.',
			'intro'    => 'An optional, stand-alone pathway: order your testing separately, then add non-medical health coaching to help you turn information into practical next steps.',
		)
	);
	?>
	<section class="section section--tight">
		<div class="container">
			<div class="alert alert--info" role="note"><strong><?php esc_html_e( 'Two separate purchases.', 'fvr' ); ?></strong> <?php echo esc_html( sprintf( 'If you choose both, the combined cost is %1$s: %2$s for testing paid to the lab provider, and %3$s for coaching paid to FoundVital. They are different transactions with different companies.', $total, $test_price, $coach_price ) ); ?></div>

			<div class="steps">
				<article class="step panel">
					<span class="step__num">1</span>
					<span class="card__icon"><?php echo fvr_icon( 'flask' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<h2 class="h3"><?php esc_html_e( 'Lab testing', 'fvr' ); ?> <span class="price-tag"><?php echo esc_html( $test_price ); ?></span></h2>
					<p><?php esc_html_e( 'Purchased directly from our partner lab shop (OutfitMD affiliate). That purchase is handled entirely by the external provider, not by FoundVital.', 'fvr' ); ?></p>
					<p><?php esc_html_e( 'Medical services and lab result interpretation are provided by licensed providers through the physician service associated with OutfitMD.', 'fvr' ); ?></p>
					<?php fvr_pending( 'Physician-review process and final wording still need client confirmation before this page is published.' ); ?>
					<div class="btn-row"><?php fvr_btn( __( 'Order testing (external shop)', 'fvr' ), $shop, 'secondary', 'outfitmd_click' ); ?></div>
				</article>

				<article class="step panel">
					<span class="step__num">2</span>
					<span class="card__icon"><?php echo fvr_icon( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<h2 class="h3"><?php esc_html_e( 'Optional coaching session', 'fvr' ); ?> <span class="price-tag"><?php echo esc_html( $coach_price ); ?></span></h2>
					<p><?php echo esc_html( sprintf( 'Non-medical health coaching with %s through FoundVital: practical education and planning around your next steps. This is the only payment FoundVital collects, through Stripe\'s secure hosted checkout.', fvr_opt( 'founder_name' ) ) ); ?></p>
					<?php if ( $stripe ) : ?>
						<div class="btn-row"><?php fvr_btn( __( 'Book coaching (secure Stripe checkout)', 'fvr' ), $stripe, 'primary', 'stripe_checkout_click_clarity' ); ?></div>
					<?php else : ?>
						<p class="muted"><?php esc_html_e( 'Coaching checkout will be available soon.', 'fvr' ); ?></p>
						<?php fvr_pending( 'Add the Stripe Payment Link for the $100 coaching session (Reset Settings > Links & checkout).' ); ?>
					<?php endif; ?>
				</article>
			</div>

			<div class="panel mt">
				<h2 class="h3"><?php esc_html_e( 'Important to know', 'fvr' ); ?></h2>
				<ul class="check-list check-list--small">
					<li><?php echo fvr_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'FoundVital coaching is non-medical. It is not diagnosis, treatment, or prescribing.', 'fvr' ); ?></span></li>
					<li><?php echo fvr_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'This pathway stands on its own. Purchasing it does not enroll you in the 12-week Reset program.', 'fvr' ); ?></span></li>
					<li><?php echo fvr_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'If you\'d like to explore Reset afterwards, a fit conversation is available. It\'s entirely optional.', 'fvr' ); ?></span></li>
				</ul>
				<div class="btn-row"><?php fvr_fit_btn( 'ghost', __( 'Curious about Reset? Book a fit conversation', 'fvr' ), 'cta_fit_call_clarity' ); ?></div>
			</div>
		</div>
	</section>
	<?php
endwhile;
get_footer();
