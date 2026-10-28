<?php
/**
 * Four-phase method: a 12-week roadmap plus one card per phase, and an optional authentic sample
 * (Welcome Kit / Blueprint / three-commitment worksheet) supplied by the client.
 *
 * @package foundvital-reset
 */

$phases = apply_filters(
	'fvr_phases',
	array(
		array( 'Regulate', 'Weeks 1–3', 'Everyday nutrition, sleep, stress, and recovery routines.', 1, 3 ),
		array( 'Restore', 'Weeks 4–6', 'Midlife health education, hormone-related topics, and recovery.', 4, 6 ),
		array( 'Rebuild', 'Weeks 7–9', 'Strength, muscle health, protein, and progressive movement.', 7, 9 ),
		array( 'Optimize', 'Weeks 10–12', 'Review progress, strengthen sustainable habits, and create a forward plan.', 10, 12 ),
	)
);

$sample_id  = (int) fvr_field( 'phases_sample_image', 0 );
$sample     = $sample_id ? fvr_attachment( $sample_id, 'large', array( 'class' => 'sample__img', 'loading' => 'lazy' ) ) : '';
$sample_cap = (string) fvr_field( 'phases_sample_caption', 'A look inside the program materials.' );
?>
<section class="section section--ice" aria-labelledby="phases-title">
	<div class="container">
		<div class="section__head reveal">
			<p class="eyebrow"><?php esc_html_e( 'The four-phase method', 'fvr' ); ?></p>
			<h2 id="phases-title"><?php esc_html_e( 'One clear path through twelve weeks.', 'fvr' ); ?></h2>
			<p class="muted"><?php esc_html_e( 'Each phase is a learning emphasis, not a locked door. You have access to the full content library throughout the program.', 'fvr' ); ?></p>
		</div>

		<ol class="journey">
			<?php foreach ( $phases as $i => $ph ) : ?>
				<li class="stage p<?php echo esc_attr( (string) ( $i + 1 ) ); ?> reveal" style="--d:<?php echo esc_attr( (string) ( $i * 90 ) ); ?>ms">
					<div class="stage__head">
						<span class="stage__phase"><?php echo esc_html( sprintf( 'Phase %d', $i + 1 ) ); ?></span>
						<span class="stage__weeks"><span class="stage__weeks-label"><?php echo esc_html( $ph[3] === $ph[4] ? 'Week' : 'Weeks' ); ?></span> <?php echo esc_html( $ph[3] . '–' . $ph[4] ); ?></span>
					</div>
					<div class="stage__body">
						<h3><?php echo esc_html( $ph[0] ); ?></h3>
					<p><?php echo esc_html( $ph[2] ); ?></p>
						<span class="stage__dots" aria-hidden="true">
							<?php for ( $w = 1; $w <= 12; $w++ ) : ?>
								<i<?php echo ( $w >= $ph[3] && $w <= $ph[4] ) ? ' class="on"' : ''; ?>></i>
							<?php endfor; ?>
						</span>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>

		<?php if ( $sample ) : ?>
			<figure class="sample reveal">
				<?php echo $sample; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<figcaption><?php echo esc_html( $sample_cap ); ?></figcaption>
			</figure>
		<?php else : ?>
			<?php fvr_pending( 'The guide asks for one authentic sample (Welcome Kit, Blueprint or three-commitment worksheet) here. Add it via the homepage field "Phases: authentic sample image".' ); ?>
		<?php endif; ?>

		<div class="btn-row btn-row--center reveal">
			<?php fvr_fit_btn( 'primary', '', 'cta_fit_call_phases' ); ?>
		</div>
	</div>
</section>
