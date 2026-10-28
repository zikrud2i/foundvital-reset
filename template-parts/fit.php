<?php
/**
 * Homepage section 5 – Is Reset right for you? (fit, inclusions, pricing, FAQ).
 *
 * @package foundvital-reset
 */

$good     = fvr_lines( fvr_field( 'fit_good', "You're already caring for your health and want structure and guidance.\nYou're comfortable working independently between scheduled check-ins.\nYou'd rather choose a few practical priorities than follow another complicated plan." ) );
$not      = fvr_lines( fvr_field( 'fit_not', "A diagnostic, treatment, or medication-access service.\nA guarantee of rapid weight loss or any specific result.\nA replacement for care from your licensed healthcare provider." ) );
$included = fvr_lines( fvr_field( 'fit_included', "Onboarding, goal-setting, and three starting commitments\nProtein and meal-planning resources\nProgressive movement and strength training\nEducation on sleep, stress, recovery, and midlife health\nScheduled personalized coaching check-ins\nThe FoundVital Blueprint, Welcome Kit, and worksheets\nA full-term learning library and private member community" ) );
$weekly   = (string) fvr_field( 'fit_weekly', '' );
?>
<section class="section section--sand" id="fit-and-investment" aria-labelledby="fit-title">
	<div class="container">
		<div class="section__head reveal">
			<p class="eyebrow"><?php esc_html_e( 'Program fit & investment', 'fvr' ); ?></p>
			<h2 id="fit-title"><?php echo esc_html( fvr_field( 'fit_heading', 'Is Reset the kind of support you\'re looking for?' ) ); ?></h2>
		</div>

		<div class="fit-grid">
			<div class="panel reveal">
				<h3><?php esc_html_e( 'Reset may suit you if…', 'fvr' ); ?></h3>
				<ul class="check-list">
					<?php foreach ( $good as $l ) : ?>
						<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $l ); ?></span></li>
					<?php endforeach; ?>
				</ul>
				<h3 class="mt"><?php esc_html_e( 'Reset is not…', 'fvr' ); ?></h3>
				<ul class="check-list check-list--no">
					<?php foreach ( $not as $l ) : ?>
						<li><?php echo fvr_icon( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $l ); ?></span></li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $weekly ) : ?>
					<p class="callout"><strong><?php esc_html_e( 'Weekly participation:', 'fvr' ); ?></strong> <?php echo esc_html( $weekly ); ?></p>
				<?php else : ?>
					<?php fvr_pending( 'Weekly time commitment is hidden until confirmed (Homepage fields > "Fit: weekly participation note").' ); ?>
				<?php endif; ?>
			</div>

			<div class="panel panel--include reveal" style="--d:90ms">
				<h3><?php esc_html_e( 'What\'s included', 'fvr' ); ?></h3>
				<ul class="check-list">
					<?php foreach ( $included as $l ) : ?>
						<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $l ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<?php
		$dummy_items = (array) get_option( 'fvr_dummy_items', array() );
		if ( $dummy_items ) {
			fvr_pending( 'This section currently contains SAMPLE content (price, terms, check-ins, FAQ answers) for design review only. Replace it with approved content before launch. See Reset Settings > Launch readiness.' );
		}
		?>
		<?php get_template_part( 'template-parts/pricing' ); ?>

		<div class="faq reveal" id="faq">
			<h3 class="faq__title"><?php esc_html_e( 'Frequently asked questions', 'fvr' ); ?></h3>
			<?php
			ob_start();
			$faq_count = fvr_render_faqs();
			$faq_html  = ob_get_clean();
			if ( $faq_count ) {
				echo $faq_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside fvr_render_faqs().
			}
			fvr_pending( $faq_count ? 'Some FAQ entries are still drafts awaiting approved answers (Reset FAQ in the admin menu).' : 'No FAQ entries are published yet (Reset FAQ in the admin menu).' );
			?>
		</div>

		<div class="btn-row btn-row--center reveal">
			<?php fvr_fit_btn( 'primary', __( 'Schedule a Reset Fit Conversation', 'fvr' ), 'cta_fit_call_fit' ); ?>
		</div>
	</div>
</section>
