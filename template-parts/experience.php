<?php
/**
 * Homepage section 3 – The 12-Week Reset Experience (four feature tiles).
 *
 * @package foundvital-reset
 */

$checkins = fvr_lines( fvr_opt( 'checkin_details' ) );
$cards    = array(
	array( 'compass', 'Your Starting Point', fvr_field( 'exp1_text', 'Begin with a one-hour 1:1 onboarding call with Tara, your goals, and baseline information, plus three core commitments that help you choose practical priorities.' ), array( 'Onboarding call', 'Your goals', 'Baseline information', '3 core commitments' ) ),
	array( 'leaf', 'Nutrition, Strength, and Recovery', fvr_field( 'exp2_text', 'Protein and meal-planning resources, progressive movement and strength training, and education on sleep, stress, recovery, and midlife health.' ), array( 'Protein & meal planning', 'Strength training', 'Sleep & stress', 'Recovery' ) ),
	array( 'chat', 'Coaching and Accountability', fvr_field( 'exp3_text', 'A one-hour 1:1 onboarding call, then five scheduled 30-minute check-ins with Tara to review how things are going, ask questions, and make adjustments.' ), array( '1:1 onboarding', '5 check-ins', 'Questions', 'Adjustments' ) ),
	array( 'toolkit', 'Tools and Community', fvr_field( 'exp4_text', 'The FoundVital Blueprint, your personalized plan for the whole 12-week Reset, prepared by Tara. Plus the Welcome Kit, worksheets, your smart scale, a full-term learning library, and a private member community.' ), array( 'FoundVital Blueprint', 'Welcome Kit', 'Smart scale', 'Learning library', 'Private community' ) ),
);
?>
<section class="section" id="how-reset-works" aria-labelledby="exp-title">
	<div class="container exp">
		<div class="exp__intro reveal">
			<p class="eyebrow"><?php esc_html_e( 'The 12-week experience', 'fvr' ); ?></p>
			<h2 id="exp-title"><?php echo esc_html( fvr_field( 'exp_heading', 'More than information. A structured way to put it into practice.' ) ); ?></h2>
			<ul class="exp__stats" aria-label="<?php esc_attr_e( 'Program at a glance', 'fvr' ); ?>">
				<li><strong>12</strong><span><?php esc_html_e( 'weeks', 'fvr' ); ?></span></li>
				<li><strong>4</strong><span><?php esc_html_e( 'phases', 'fvr' ); ?></span></li>
				<li><strong>1</strong><span><?php esc_html_e( 'practical approach', 'fvr' ); ?></span></li>
			</ul>
		</div>

		<div class="exp__grid">
			<?php foreach ( $cards as $i => $c ) : ?>
				<article class="tile<?php echo 2 === $i ? ' tile--feature' : ''; ?> reveal" style="--d:<?php echo esc_attr( (string) ( $i * 70 ) ); ?>ms">
					<div class="tile__top">
						<span class="tile__icon"><?php echo fvr_icon( $c[0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="tile__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					</div>
					<h3><?php echo esc_html( $c[1] ); ?></h3>
					<p><?php echo esc_html( $c[2] ); ?></p>
					<ul class="tags" aria-label="<?php esc_attr_e( 'Includes', 'fvr' ); ?>">
						<?php foreach ( $c[3] as $tag ) : ?>
							<li><?php echo esc_html( $tag ); ?></li>
						<?php endforeach; ?>
					</ul>
					<?php if ( 2 === $i ) : ?>
						<?php if ( $checkins ) : ?>
							<ul class="check-list check-list--small tile__details">
								<?php foreach ( $checkins as $line ) : ?>
									<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $line ); ?></span></li>
								<?php endforeach; ?>
							</ul>
						<?php else : ?>
							<?php fvr_pending( 'Add the confirmed check-in frequency, format, provider and between-check-in support (Reset Settings > Pricing).' ); ?>
						<?php endif; ?>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
