<?php
/**
 * Homepage hero.
 *
 * @package foundvital-reset
 */

$points = fvr_lines( fvr_field( 'hero_points', "A clear, structured 12-week program\nSelf-paced learning and practical tools\nScheduled personalized coaching check-ins" ) );
$photo  = fvr_founder_photo( 'fvr-portrait', array( 'class' => 'hero__img', 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(min-width: 900px) 480px, 90vw' ) );
?>
<section class="hero" aria-labelledby="hero-title">
	<div class="container hero__grid">
		<div class="hero__copy">
			<p class="eyebrow"><?php echo esc_html( fvr_field( 'hero_eyebrow', 'FoundVital Reset™ · 12-week midlife health coaching' ) ); ?></p>
			<h1 id="hero-title"><?php echo fvr_hero_headline( (string) fvr_field( 'hero_headline', 'You\'re already putting effort into your health. Get a clearer way forward.' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside the helper. ?></h1>
			<p class="lead"><?php echo esc_html( fvr_field( 'hero_sub', 'FoundVital Reset is a 12-week midlife health coaching program that brings nutrition, strength, recovery, and everyday habits into one practical approach, with self-paced resources and scheduled coaching support along the way.' ) ); ?></p>

			<ul class="pill-list">
				<?php foreach ( $points as $pt ) : ?>
					<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $pt ); ?></li>
				<?php endforeach; ?>
			</ul>

			<div class="btn-row">
				<?php
				fvr_fit_btn( 'primary', '', 'cta_fit_call_hero' );
				fvr_quiz_btn( 'secondary', '', 'cta_quiz_hero' );
				?>
			</div>

			<p class="hero__note"><?php echo fvr_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( fvr_field( 'hero_note', 'Health coaching and education, not medical treatment.' ) ); ?></span></p>
		</div>

		<figure class="hero__figure">
			<div class="hero__frame">
				<?php if ( $photo ) : ?>
					<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php else : ?>
					<div class="hero__placeholder" role="img" aria-label="<?php esc_attr_e( 'Photo of Tara coming soon', 'fvr' ); ?>">
						<span><?php echo esc_html( (string) fvr_opt( 'founder_name' ) ); ?></span>
					</div>
					<?php fvr_pending( 'Upload an approved portrait of Tara in Reset Settings > Founder & brand.' ); ?>
				<?php endif; ?>
			</div>
			<figcaption class="hero__badge">
				<strong><?php echo esc_html( (string) fvr_opt( 'founder_name' ) ); ?></strong>
				<span><?php echo esc_html( (string) fvr_opt( 'founder_credentials' ) ); ?></span>
				<small><?php esc_html_e( 'Founder, FoundVital Reset', 'fvr' ); ?></small>
			</figcaption>
		</figure>
	</div>
</section>
