<?php
/**
 * Homepage section 6 – Final invitation (also reusable on inner pages).
 *
 * @package foundvital-reset
 */

$heading = fvr_field( 'final_heading', 'Your next chapter doesn\'t need another complicated plan.' );
$text    = fvr_field( 'final_text', 'If you\'re looking for a more structured way to approach nutrition, strength, recovery, and everyday habits, Reset may be a good next step. A fit conversation gives you time to ask questions and decide whether the experience matches what you\'re looking for.' );
?>
<section class="cta-band" aria-labelledby="final-title">
	<div class="container container--narrow text-center reveal">
		<h2 id="final-title"><?php echo esc_html( $heading ); ?></h2>
		<p><?php echo esc_html( $text ); ?></p>
		<div class="btn-row btn-row--center">
			<?php
			fvr_fit_btn( 'gold', '', 'cta_fit_call_final' );
			fvr_quiz_btn( 'light', __( 'Not Ready Yet? Take the Reset Quiz', 'fvr' ), 'cta_quiz_final' );
			?>
		</div>
		<p class="cta-band__note"><?php esc_html_e( 'No pressure. Decide on your own timeline.', 'fvr' ); ?></p>
	</div>
</section>
