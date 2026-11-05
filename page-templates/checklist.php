<?php
/**
 * Template Name: Reset – Free Checklist
 *
 * Copy follows "Checklist & Quiz Landing Page Copy – Tara Review, October 2026".
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$benefits = fvr_lines( fvr_field( 'page_points', "Seven thoughtful questions you can work through on your own.\nSpace to identify what's working, what's getting in the way, and what you'd like to explore next.\nThree small commitments to take with you into the week ahead." ) );
	$prev_id  = (int) fvr_field( 'page_preview_image', 0 );
	$preview  = $prev_id ? fvr_attachment( $prev_id, 'large', array( 'class' => 'checklist-preview__img', 'loading' => 'lazy' ) ) : '';
	if ( ! $preview ) {
		$preview = '<img class="checklist-preview__img" src="' . esc_url( FVR_URI . '/assets/images/checklist-preview.jpg' ) . '" width="1100" height="1424" loading="lazy" decoding="async" alt="' . esc_attr__( 'Page 1 of the Seven Questions checklist: the title, a short introduction, and the first two questions with lined space for notes.', 'fvr' ) . '">';
	}
	$consent = 'Yes, I want to subscribe to FoundVital emails with practical wellness content, program news, event invitations, and occasional promotional offers. I understand I can unsubscribe at any time.';
	$photo   = fvr_founder_photo( 'thumbnail', array( 'class' => 'tara-note__img', 'loading' => 'lazy' ) );
	?>
	<section class="page-hero page-hero--split">
		<div class="container split">
			<div class="split__copy">
				<?php fvr_breadcrumbs(); ?>
				<p class="eyebrow"><?php echo esc_html( fvr_field( 'page_eyebrow', 'Free printable midlife health checklist' ) ); ?></p>
				<h1><?php echo esc_html( fvr_field( 'page_headline', 'Before You Change Your Midlife Health Routine, Ask These Seven Questions' ) ); ?></h1>
				<p class="lead"><?php echo esc_html( fvr_field( 'page_intro', 'You\'ve saved the nutrition tips, planned the walks, and told yourself you\'d make more time for strength. Then the week gets busy, and it\'s hard to know what deserves attention first. This checklist gives you a little room to sort through what actually fits your life.' ) ); ?></p>
				<p><?php echo esc_html( fvr_field( 'page_intro2', 'This free, printable FoundVital checklist gives you a simple way to take stock of what\'s already working, choose one place to focus, and make your next step feel more manageable.' ) ); ?></p>

				<h2 class="h3 mt"><?php echo esc_html( fvr_field( 'page_points_title', 'A practical place to start' ) ); ?></h2>
				<ul class="check-list">
					<?php foreach ( $benefits as $b ) : ?>
						<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $b ); ?></span></li>
					<?php endforeach; ?>
				</ul>
				<p class="mt"><?php echo esc_html( fvr_field( 'page_closing', 'No long list of rules. No pressure to change everything at once. Just a little room to think about what would actually be useful.' ) ); ?></p>
			</div>

			<div class="split__form">
				<div class="form-card">
					<h2 class="form-card__title"><?php esc_html_e( 'Get the checklist and stay connected with FoundVital', 'fvr' ); ?></h2>
					<p class="form-card__intro"><?php esc_html_e( 'Subscribe to FoundVital emails for practical health and wellness education, program updates, event invitations, and occasional promotional offers. When you sign up, you\'ll get immediate access to the printable checklist, and a copy will arrive by email so you can return to it later.', 'fvr' ); ?></p>
					<?php
					fvr_lead_form(
						'checklist',
						__( 'Subscribe & Get My Free Checklist', 'fvr' ),
						$consent,
						__( 'The free checklist is available when you subscribe to FoundVital emails. Unsubscribe anytime.', 'fvr' )
					);
					?>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--tight">
		<div class="container container--narrow">
			<div class="panel">
				<h2 class="h3"><?php echo esc_html( fvr_field( 'page_extra_title', 'Inside the checklist' ) ); ?></h2>
				<div class="checklist-preview">
					<figure class="checklist-preview__figure">
						<?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above. ?>
						<figcaption><?php echo esc_html( fvr_field( 'page_preview_caption', '3 fillable pages · 7 questions · room for your own notes.' ) ); ?></figcaption>
					</figure>
					<p class="checklist-preview__text"><?php echo esc_html( fvr_field( 'page_extra', 'Seven short reflection questions to help you decide what deserves your attention first, with room for your own notes.' ) ); ?></p>
				</div>
			</div>

			<aside class="tara-note" aria-labelledby="tara-note-title">
				<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div>
					<h2 class="h3" id="tara-note-title"><?php esc_html_e( 'A note from Tara', 'fvr' ); ?></h2>
					<blockquote>
						<p><?php esc_html_e( '“You don\'t have to start over to make progress. Sometimes a useful first step is getting clear on what already fits your life and what deserves your attention next.”', 'fvr' ); ?></p>
					</blockquote>
					<p><?php esc_html_e( 'FoundVital Reset™ brings this kind of practical reflection into a broader 12-week coaching experience, guided by a PA with 16 years of clinical experience. If you\'d like to learn more about the program, you can explore it at your own pace.', 'fvr' ); ?></p>
					<?php fvr_btn( __( 'Explore FoundVital Reset™', 'fvr' ), home_url( '/' ), 'ghost', 'cta_explore_reset_checklist' ); ?>
				</div>
			</aside>

			<p class="fineprint"><?php esc_html_e( 'This checklist is educational and is not medical advice.', 'fvr' ); ?></p>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/final-cta' );
endwhile;
get_footer();
