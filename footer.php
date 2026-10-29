<?php
/**
 * Site footer + consent banner.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$socials = array(
	'social_instagram' => 'Instagram',
	'social_facebook'  => 'Facebook',
	'social_threads'   => 'Threads',
	'social_youtube'   => 'YouTube',
);
?>
</main>

<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo fvr_logo(); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="site-logo__tag">Reset<sup>™</sup></span></a>
			<p class="site-footer__disclaimer"><?php echo esc_html( (string) fvr_opt( 'footer_disclaimer' ) ); ?></p>
			<?php
			$has_social = false;
			foreach ( $socials as $key => $label ) {
				if ( fvr_opt( $key ) ) {
					$has_social = true;
				}
			}
			if ( $has_social ) :
				?>
				<ul class="social-list" aria-label="<?php esc_attr_e( 'FoundVital on social media', 'fvr' ); ?>">
					<?php foreach ( $socials as $key => $label ) : ?>
						<?php if ( fvr_opt( $key ) ) : ?>
							<li><a href="<?php echo esc_url( (string) fvr_opt( $key ) ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $label ); ?>"><?php echo fvr_icon( strtolower( $label ), 'icon icon--social' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php echo esc_html( $label ); ?> <?php esc_html_e( '(opens in a new tab)', 'fvr' ); ?></span></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( fvr_opt( 'support_email' ) ) : ?>
				<p class="site-footer__email"><a href="mailto:<?php echo esc_attr( antispambot( (string) fvr_opt( 'support_email' ) ) ); ?>"><?php echo esc_html( antispambot( (string) fvr_opt( 'support_email' ) ) ); ?></a></p>
			<?php endif; ?>
		</div>

		<nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer', 'fvr' ); ?>">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Explore', 'fvr' ); ?></h2>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'footer-list', 'depth' => 1 ) );
			} else {
				echo '<ul class="footer-list">';
				$links = array(
					array( 'The 12-week program', home_url( '/#how-reset-works' ) ),
					array( 'Reset Fit Conversation', fvr_page_url( 'fit-call' ) ),
				);
				foreach ( $links as $l ) {
					echo '<li><a href="' . esc_url( $l[1] ) . '">' . esc_html( $l[0] ) . '</a></li>';
				}
				echo '</ul>';
			}
			?>
		</nav>
	</div>

	<div class="container site-footer__legal">
		<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> FoundVital. <?php esc_html_e( 'All rights reserved.', 'fvr' ); ?> <?php esc_html_e( 'Developed by', 'fvr' ); ?> <a href="https://d2itechnology.com/" target="_blank" rel="noopener">D2i Technology</a></p>
		<ul class="legal-list">
			<?php
			foreach ( array( 'privacy-policy' => 'Privacy Policy', 'terms' => 'Terms' ) as $slug => $label ) {
				$p = get_page_by_path( $slug );
				if ( $p && 'publish' === $p->post_status ) {
					echo '<li><a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $label ) . '</a></li>';
				}
			}
			?>
		</ul>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
