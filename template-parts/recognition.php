<?php
/**
 * Homepage section 2 – Recognition.
 *
 * @package foundvital-reset
 */
?>
<section class="section section--sand" aria-labelledby="recog-title">
	<div class="container container--mid text-center reveal">
		<p class="eyebrow"><?php esc_html_e( 'Sound familiar?', 'fvr' ); ?></p>
		<h2 id="recog-title"><?php echo esc_html( fvr_field( 'recog_heading', 'When the routines that used to work don\'t feel quite as effective anymore.' ) ); ?></h2>
		<p><?php echo esc_html( fvr_field( 'recog_p1', 'You may already know a lot about nutrition, movement, and taking care of yourself. But knowing what matters and figuring out how to put it into practice are two different things.' ) ); ?></p>
		<p><?php echo esc_html( fvr_field( 'recog_p2', 'Reset brings those pieces together in a more structured, practical way, helping you choose the most useful priorities and make them workable in a busy life.' ) ); ?></p>
		<div class="btn-row btn-row--center">
			<?php fvr_btn( __( 'See How Reset Works', 'fvr' ), home_url( '/#how-reset-works' ), 'secondary', 'cta_how_it_works' ); ?>
		</div>
	</div>
</section>
