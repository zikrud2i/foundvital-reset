<?php
/**
 * Template Name: Reset – Fit Conversation
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$cal    = (string) fvr_opt( 'calendly_url' );
	$embed  = (bool) fvr_opt( 'calendly_embed' ) && $cal;
	$expect = fvr_lines( fvr_field( 'page_points', "Time to ask your questions about Reset\nA relaxed conversation about your goals and what support you're looking for\nA clear picture of how the program works, what's included, and what it asks of you\nNo pressure: you decide on your own timeline" ) );
	$prep   = fvr_lines( fvr_field( 'page_extra', "Anything you'd like to know about how check-ins and the learning library work\nYour main health and routine goals for the next few months\nQuestions about fit, time, and budget" ) );
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow'  => 'Reset Fit Conversation',
			'headline' => 'Let\'s see if Reset is the right fit.',
			'intro'    => 'A fit conversation gives you time to ask questions and decide whether the experience matches what you\'re looking for.',
		)
	);
	?>
	<section class="section section--tight">
		<div class="container fit-call">
			<div class="fit-call__info">
				<div class="panel">
					<h2 class="h3"><?php echo esc_html( fvr_field( 'page_points_title', 'What to expect' ) ); ?></h2>
					<ul class="check-list">
						<?php foreach ( $expect as $l ) : ?>
							<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $l ); ?></span></li>
						<?php endforeach; ?>
					</ul>
					<h2 class="h3 mt"><?php echo esc_html( fvr_field( 'page_extra_title', 'Helpful to think about beforehand' ) ); ?></h2>
					<ul class="check-list check-list--small">
						<?php foreach ( $prep as $l ) : ?>
							<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $l ); ?></span></li>
						<?php endforeach; ?>
					</ul>
					<p class="callout"><?php esc_html_e( 'A fit conversation is a coaching and program-fit conversation. It is not a medical appointment, and no diagnosis or treatment advice is given.', 'fvr' ); ?></p>
				</div>

				<ol class="how-steps">
					<li><span>1</span><div><strong><?php esc_html_e( 'Pick a time', 'fvr' ); ?></strong><?php esc_html_e( 'Choose a slot that suits you in the booking calendar.', 'fvr' ); ?></div></li>
					<li><span>2</span><div><strong><?php esc_html_e( 'Get your confirmation', 'fvr' ); ?></strong><?php esc_html_e( 'Calendly emails you the details, and you can reschedule if you need to.', 'fvr' ); ?></div></li>
					<li><span>3</span><div><strong><?php esc_html_e( 'Talk it through', 'fvr' ); ?></strong><?php esc_html_e( 'Ask your questions and decide whether Reset fits.', 'fvr' ); ?></div></li>
				</ol>

				<div class="host-card">
					<?php echo fvr_founder_photo( 'thumbnail', array( 'class' => 'host-card__img', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div>
						<p class="host-card__label"><?php esc_html_e( 'Your conversation is with', 'fvr' ); ?></p>
						<p class="host-card__name"><?php echo esc_html( fvr_founder_line() ); ?></p>
						<p class="host-card__links"><?php esc_html_e( 'Not ready yet?', 'fvr' ); ?> <a href="<?php echo esc_url( fvr_page_url( 'quiz' ) ); ?>"><?php esc_html_e( 'Take the quiz', 'fvr' ); ?></a> · <a href="<?php echo esc_url( fvr_page_url( 'checklist' ) ); ?>"><?php esc_html_e( 'Get the checklist', 'fvr' ); ?></a></p>
					</div>
				</div>
			</div>

			<div class="fit-call__book">
				<div class="form-card" id="book">
					<h2 class="form-card__title"><?php esc_html_e( 'Choose a time', 'fvr' ); ?></h2>
					<?php if ( $cal ) : ?>
						<?php fvr_btn( __( 'Open the booking calendar', 'fvr' ), $cal, 'primary', 'calendly_open_click', 'btn--block' ); ?>
						<p class="fineprint"><?php esc_html_e( 'Scheduling is handled securely by Calendly and opens in a new tab.', 'fvr' ); ?></p>
						<?php if ( $embed ) : ?>
							<div class="calendly" data-calendly-url="<?php echo esc_url( $cal ); ?>" aria-live="polite">
								<noscript><p><?php esc_html_e( 'JavaScript is needed to show the calendar here. Please use the button above.', 'fvr' ); ?></p></noscript>
							</div>
						<?php endif; ?>
					<?php else : ?>
						<p><?php esc_html_e( 'Online booking is being set up. Please check back soon.', 'fvr' ); ?></p>
						<?php fvr_pending( 'Add the Calendly URL in Reset Settings > Links & checkout.' ); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/final-cta' );
endwhile;
get_footer();
