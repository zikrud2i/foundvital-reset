<?php
/**
 * Template Name: Reset – Client Assessment (Jotform)
 * Onboarding form for enrolled clients ("Your Reset Starting Point"), linked from the Welcome Kit QR code.
 * Answers stay in Jotform: the form runs in Jotform's own frame, so nothing reaches WordPress or Analytics.
 * Noindex (inc/security.php), no Meta Pixel, not in any menu.
 *
 * @package foundvital-reset
 */

$jotform_id = '262897504058063';

get_header();
while ( have_posts() ) :
	the_post();
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow'  => 'For enrolled clients',
			'headline' => get_the_title(),
			'intro'    => fvr_field( 'page_intro', 'Please complete this before your onboarding call with Tara. Your answers go securely to FoundVital and help shape your FoundVital Blueprint.' ),
		)
	);
	?>
	<section class="section section--tight">
		<div class="container container--narrow">
			<iframe id="JotFormIFrame-<?php echo esc_attr( $jotform_id ); ?>" class="jotform-embed" title="<?php echo esc_attr( get_the_title() ); ?>" src="<?php echo esc_url( 'https://form.jotform.com/' . $jotform_id ); ?>" allow="fullscreen" scrolling="no"></iframe>
			<p class="fineprint"><?php esc_html_e( 'Trouble seeing the form?', 'fvr' ); ?> <a href="<?php echo esc_url( 'https://form.jotform.com/' . $jotform_id ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open it in a new tab', 'fvr' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'fvr' ); ?></span></a></p>
		</div>
	</section>
	<script src="https://cdn.jotfor.ms/s/umd/latest/for-form-embed-handler.js"></script>
	<script>window.jotformEmbedHandler && window.jotformEmbedHandler("iframe[id='JotFormIFrame-<?php echo esc_js( $jotform_id ); ?>']", "https://form.jotform.com/");</script>
	<?php
endwhile;
get_footer();
