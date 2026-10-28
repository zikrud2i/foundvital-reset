<?php
/**
 * 404.
 *
 * @package foundvital-reset
 */

get_header();
?>
<section class="page-hero">
	<div class="container container--narrow text-center">
		<p class="eyebrow"><?php esc_html_e( 'Page not found', 'fvr' ); ?></p>
		<h1><?php esc_html_e( 'That page isn\'t here.', 'fvr' ); ?></h1>
		<p class="lead"><?php esc_html_e( 'The link may be out of date. Here are some good places to start.', 'fvr' ); ?></p>
		<div class="btn-row btn-row--center">
			<?php
			fvr_btn( __( 'Go to the Reset homepage', 'fvr' ), home_url( '/' ), 'primary' );
			fvr_quiz_btn();
			?>
		</div>
	</div>
</section>
<?php
get_footer();
