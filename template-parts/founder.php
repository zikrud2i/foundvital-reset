<?php
/**
 * Homepage section 4 – Meet Tara.
 *
 * @package foundvital-reset
 */

$photo = fvr_founder_photo( 'fvr-portrait', array( 'class' => 'founder__img', 'loading' => 'lazy', 'sizes' => '(min-width: 900px) 420px, 90vw' ) );
$creds = array_filter( array_map( 'trim', explode( ',', (string) fvr_opt( 'founder_credentials' ) ) ) );
$exp   = (string) fvr_opt( 'founder_experience' );
$video = (string) fvr_opt( 'founder_video_url' );
$name  = (string) fvr_opt( 'founder_name' );

$p1 = fvr_field( 'meet_p1', 'I bring ' . implode( ' and ', $creds ) . ' credentials, clinical leadership, and lifestyle-medicine training' . ( $exp ? ', with ' . $exp . ' of experience,' : '' ) . ' to everything in Reset. I use that foundation to teach in plain language, not to hand you a résumé or a rulebook.' );
$p2 = fvr_field( 'meet_p2', 'I created Reset to bring nutrition, strength, recovery, and everyday habits together in a more structured, practical way, built on informed decisions, sustainable habits, personalization, transparency, and long-term health.' );
$p3 = fvr_field( 'meet_p3', 'Knowing the information isn\'t always enough, so my experience shapes how Reset teaches and coaches: turning what matters into steps you can actually follow.' );
$p4 = fvr_field( 'meet_boundary', 'Reset is coaching and education. Separate licensed providers handle medical evaluation, prescribing, and clinical lab interpretation.' );

$blocks = array(
	array( 'shield', 'My background', $p1 ),
	array( 'compass', 'Why I created Reset', $p2 ),
	array( 'chat', 'My philosophy and role', $p3 ),
);

// Video (if a URL is set) takes the place of the portrait in the left column.
$vid_html  = '';
$vid_ratio = '16 / 9';
$vid_class = 'founder__video';
if ( $video ) {
	if ( preg_match( '/\.(mp4|webm)(\?.*)?$/i', $video ) ) {
		$vid_html = '<video controls preload="metadata" playsinline src="' . esc_url( $video ) . '"></video>';
	} else {
		$embed = wp_oembed_get( $video );
		if ( $embed ) {
			$vid_html = str_replace( '<iframe', '<iframe loading="lazy"', $embed );
			if ( preg_match( '/width="(\d+)"\s+height="(\d+)"/', $embed, $m ) && (int) $m[2] > (int) $m[1] ) {
				$vid_ratio  = (int) $m[1] . ' / ' . (int) $m[2]; // YouTube Shorts / vertical video.
				$vid_class .= ' founder__video--vertical';
			}
		}
	}
}
?>
<section class="section" id="meet-tara" aria-labelledby="meet-title">
	<div class="container founder">
		<figure class="founder__figure reveal">
			<?php if ( $vid_html ) : ?>
				<div class="<?php echo esc_attr( $vid_class ); ?>" style="aspect-ratio: <?php echo esc_attr( $vid_ratio ); ?>"><?php echo $vid_html; // phpcs:ignore WordPress.Security.EscapeOutput -- oEmbed/URL output escaped by WordPress. ?></div>
			<?php elseif ( $photo ) : ?>
				<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php else : ?>
				<div class="hero__placeholder" role="img" aria-label="<?php esc_attr_e( 'Portrait of Tara coming soon', 'fvr' ); ?>"><span><?php echo esc_html( $name ); ?></span></div>
				<?php fvr_pending( 'Upload an approved professional portrait (Reset Settings > Founder & brand).' ); ?>
			<?php endif; ?>
			<figcaption class="founder__cap">
				<strong><?php echo esc_html( $name ); ?></strong>
				<span><?php echo esc_html( implode( ' · ', $creds ) ); ?></span>
			</figcaption>
		</figure>

		<div class="founder__copy reveal">
			<p class="eyebrow"><?php echo esc_html( 'Meet ' . $name ); ?></p>
			<h2 id="meet-title"><?php echo esc_html( fvr_field( 'meet_heading', 'Clinically informed. Grounded in real life.' ) ); ?></h2>

			<ul class="chip-list" aria-label="<?php esc_attr_e( 'Credentials', 'fvr' ); ?>">
				<?php foreach ( $creds as $c ) : ?>
					<li><?php echo fvr_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $c ); ?></li>
				<?php endforeach; ?>
				<?php if ( $exp ) : ?>
					<li><?php echo fvr_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( ucfirst( $exp ) ); ?></li>
				<?php endif; ?>
			</ul>

			<div class="bio">
				<?php foreach ( $blocks as $b ) : ?>
					<div class="bio__item">
						<span class="bio__icon"><?php echo fvr_icon( $b[0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<div>
							<h3><?php echo esc_html( $b[1] ); ?></h3>
							<p><?php echo esc_html( $b[2] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<p class="boundary"><?php echo fvr_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $p4 ); ?></span></p>

			<?php if ( ! $exp ) : ?>
				<?php fvr_pending( 'Experience statement (e.g. "approximately 12 years") is hidden until verified – add it in Reset Settings > Founder & brand.' ); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
