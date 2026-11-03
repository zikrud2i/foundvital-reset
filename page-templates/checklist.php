<?php
/**
 * Template Name: Reset – Free Checklist
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$benefits = fvr_lines( fvr_field( 'page_points', "Clear priorities across nutrition, strength, recovery, and everyday habits\nA practical starting point you can use on your own\nSimple ways to decide what to focus on first" ) );
	$inside   = fvr_lines( fvr_field( 'page_extra', "What am I actually hoping to make easier?\nWhat's already working, and worth keeping?\nWhat advice have I collected but not really made my own?\nWhich topic do I most want to learn about next?\nWhat tends to throw my routine off?\nWhat's one commitment that could fit my ordinary week?\nWhat kind of support would help me follow through?" ) );
	$prev_id  = (int) fvr_field( 'page_preview_image', 0 );
	$preview  = $prev_id ? fvr_attachment( $prev_id, 'large', array( 'class' => 'checklist-preview__img', 'loading' => 'lazy' ) ) : '';
	if ( ! $preview ) {
		$preview = '<img class="checklist-preview__img" src="' . esc_url( FVR_URI . '/assets/images/checklist-preview.jpg' ) . '" width="1100" height="1424" loading="lazy" decoding="async" alt="' . esc_attr__( 'Page 1 of the Seven Questions checklist: the title, a short introduction, and the first two questions with lined space for notes.', 'fvr' ) . '">';
	}
	?>
	<section class="page-hero page-hero--split">
		<div class="container split">
			<div class="split__copy">
				<?php fvr_breadcrumbs(); ?>
				<p class="eyebrow"><?php echo esc_html( fvr_field( 'page_eyebrow', 'Free resource' ) ); ?></p>
				<h1><?php echo esc_html( fvr_field( 'page_headline', 'Seven Questions to Ask Before You Change Your Midlife Health Routine' ) ); ?></h1>
				<p class="lead"><?php echo esc_html( fvr_field( 'page_intro', 'A free, practical checklist to help you see where to focus first as you build a routine that fits your real life.' ) ); ?></p>
				<ul class="check-list">
					<?php foreach ( $benefits as $b ) : ?>
						<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $b ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="split__form">
				<div class="form-card">
					<h2 class="form-card__title"><?php esc_html_e( 'Send me the checklist', 'fvr' ); ?></h2>
					<?php fvr_lead_form( 'checklist', __( 'Get the Free Checklist', 'fvr' ) ); ?>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--tight">
		<div class="container container--narrow">
			<div class="panel">
				<h2 class="h3"><?php echo esc_html( fvr_field( 'page_extra_title', 'The seven questions inside' ) ); ?></h2>
				<div class="checklist-preview">
					<figure class="checklist-preview__figure">
						<?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above. ?>
						<figcaption><?php echo esc_html( fvr_field( 'page_preview_caption', '3 fillable pages · 7 questions · room for your own notes.' ) ); ?></figcaption>
					</figure>
					<?php if ( $inside ) : ?>
						<ol class="checklist-preview__list">
							<?php foreach ( $inside as $i ) : ?>
								<li><?php echo esc_html( $i ); ?></li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>
				</div>
			</div>
			<p class="fineprint"><?php esc_html_e( 'This checklist is educational and is not medical advice.', 'fvr' ); ?></p>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/final-cta' );
endwhile;
get_footer();
