<?php
/**
 * Template Name: Reset – Quiz
 *
 * Copy follows "Checklist & Quiz Landing Page Copy – Tara Review, October 2026". The required email
 * subscription comes first ("Start My Free Reset Quiz"), then the seven questions, then the result.
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$questions = fvr_get_quiz_questions();
	$total     = count( $questions );
	$capture   = 'off' !== fvr_opt( 'quiz_capture', 'required' );
	$explore   = fvr_lines( fvr_field( 'page_points', "Whether you're looking for a clearer starting point around nutrition, strength, sleep, and recovery.\nWhether making healthy habits fit your schedule is the harder part.\nWhether you want to understand what guidance and accountability from a coach would involve." ) );
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow'  => 'Free FoundVital Reset™ quiz',
			'headline' => 'What\'s Getting in the Way of a Health Routine That Works for You?',
			'intro'    => 'Maybe your phone is full of saved health ideas and your calendar is full of everyone else\'s plans. You want to follow through, but is the harder part knowing what to prioritize, making a habit fit a busy week, or figuring out whether structured coaching would help?',
		)
	);
	$live = fvr_quiz_visible() && $total;
	?>
	<section class="section section--tight" id="quiz">
		<div class="container <?php echo $live ? 'split' : 'container--narrow'; ?>">
			<?php if ( ! fvr_quiz_visible() ) : ?>
				<div class="panel text-center">
					<h2 class="h3"><?php esc_html_e( 'The quiz is almost ready.', 'fvr' ); ?></h2>
					<p><?php esc_html_e( 'In the meantime, the free checklist is a good place to start.', 'fvr' ); ?></p>
					<div class="btn-row btn-row--center">
						<?php
						fvr_btn( __( 'Get the Free Checklist', 'fvr' ), fvr_page_url( 'checklist' ), 'primary' );
						fvr_fit_btn( 'secondary' );
						?>
					</div>
				</div>
			<?php elseif ( ! $total ) : ?>
				<p><?php esc_html_e( 'No quiz questions are published yet.', 'fvr' ); ?></p>
				<?php fvr_pending( 'Add questions under Reset Quiz in the admin menu.' ); ?>
			<?php else : ?>
				<div class="split__copy">
					<p class="lead"><?php echo esc_html( fvr_field( 'page_intro2', 'In seven short questions, reflect on your everyday challenges and find a practical place to start. Your result will appear immediately on one of three FoundVital result pages, and we\'ll also email you a link to your specific result. Each result includes a useful exercise and a little more context about the 12-week FoundVital Reset™ coaching program.' ) ); ?></p>

					<h2 class="h3 mt"><?php echo esc_html( fvr_field( 'page_points_title', 'What you\'ll explore' ) ); ?></h2>
					<ul class="check-list">
						<?php foreach ( $explore as $l ) : ?>
							<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $l ); ?></span></li>
						<?php endforeach; ?>
					</ul>

					<h2 class="h3 mt"><?php esc_html_e( 'You\'ll leave with something useful', 'fvr' ); ?></h2>
					<p><?php echo esc_html( fvr_field( 'page_extra', 'Your result will be one of three starting points: Build Your Foundations, Create a Workable Routine, or Explore Structured Coaching. Each offers a simple exercise you can try on your own, plus the option to learn more about Reset.' ) ); ?></p>
				</div>

				<div class="split__form">
					<div class="form-card">
						<?php if ( ! fvr_opt( 'quiz_live' ) ) : ?>
							<div class="alert alert--warn"><strong><?php esc_html_e( 'Editor preview.', 'fvr' ); ?></strong> <?php esc_html_e( 'The quiz is not live for the public yet.', 'fvr' ); ?></div>
						<?php endif; ?>

						<form class="quiz" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-questions="<?php echo esc_attr( (string) $total ); ?>" data-intro="<?php echo $capture ? '1' : '0'; ?>">
							<?php fvr_render_form_error(); ?>
							<input type="hidden" name="action" value="fvr_quiz">
							<input type="hidden" name="fvr_return" value="<?php echo esc_attr( (string) get_the_ID() ); ?>">
							<?php
							wp_nonce_field( 'fvr_quiz', '_fvr_nonce' );
							fvr_spam_fields();
							?>

							<div class="quiz__progress" hidden>
								<div class="quiz__bar" role="progressbar" aria-valuemin="0" aria-valuemax="<?php echo esc_attr( (string) $total ); ?>" aria-valuenow="0" aria-label="<?php esc_attr_e( 'Quiz progress', 'fvr' ); ?>"><span></span></div>
								<p class="quiz__count" aria-live="polite"></p>
							</div>

							<?php if ( $capture ) : ?>
								<fieldset class="quiz__step quiz__step--intro" data-step="0">
									<legend><span class="quiz__num"><?php esc_html_e( 'Free quiz', 'fvr' ); ?></span><?php esc_html_e( 'Start your free Reset quiz', 'fvr' ); ?></legend>
									<?php fvr_required_note(); ?>
									<p class="field__error" id="quiz-caperr" role="alert" hidden></p>
									<div class="field">
										<label for="quiz-fn"><?php esc_html_e( 'First name', 'fvr' ); ?> <span class="req" aria-hidden="true">*</span></label>
										<input id="quiz-fn" type="text" name="first_name" autocomplete="given-name" maxlength="60" required>
									</div>
									<div class="field">
										<label for="quiz-em"><?php esc_html_e( 'Email address', 'fvr' ); ?> <span class="req" aria-hidden="true">*</span></label>
										<input id="quiz-em" type="email" name="email" autocomplete="email" required>
									</div>
									<?php fvr_state_field( 'quiz' ); ?>
									<div class="field field--check">
										<input id="quiz-co" type="checkbox" name="consent" value="1" required>
										<label for="quiz-co"><?php esc_html_e( 'Yes, I want to subscribe to FoundVital emails with practical wellness education, program news, events and occasional offers. I understand I can unsubscribe at any time. A FoundVital email subscription is required to take the quiz and receive my result.', 'fvr' ); ?> <span class="req" aria-hidden="true">*</span></label>
									</div>
								</fieldset>
							<?php endif; ?>

							<?php
							$n = 0;
							foreach ( $questions as $qid => $q ) :
								++$n;
								?>
								<fieldset class="quiz__step" data-step="<?php echo esc_attr( (string) $n ); ?>">
									<legend><span class="quiz__num"><?php echo esc_html( sprintf( 'Question %d of %d', $n, $total ) ); ?></span><?php echo esc_html( $q['q'] ); ?></legend>
									<div class="quiz__options">
										<?php foreach ( $q['answers'] as $ai => $a ) : ?>
											<label class="option">
												<input type="radio" name="fvr_a[<?php echo esc_attr( (string) $qid ); ?>]" value="<?php echo esc_attr( (string) $ai ); ?>" required>
												<span class="option__box"><?php echo esc_html( $a['text'] ); ?></span>
											</label>
										<?php endforeach; ?>
									</div>
									<p class="field__error" role="alert" hidden><?php esc_html_e( 'Please choose an answer to continue.', 'fvr' ); ?></p>
								</fieldset>
							<?php endforeach; ?>

							<div class="quiz__nav">
								<button type="button" class="btn btn--ghost" data-quiz-back hidden><span><?php esc_html_e( 'Back', 'fvr' ); ?></span></button>
								<button type="button" class="btn btn--primary" data-quiz-next hidden data-label-start="<?php esc_attr_e( 'Start My Free Reset Quiz', 'fvr' ); ?>" data-label-next="<?php esc_attr_e( 'Next', 'fvr' ); ?>"><span><?php esc_html_e( 'Next', 'fvr' ); ?></span><?php echo fvr_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
								<button type="submit" class="btn btn--primary" data-quiz-submit><span><?php esc_html_e( 'See My Result', 'fvr' ); ?></span><?php echo fvr_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
							</div>

							<p class="fineprint"><?php esc_html_e( 'Takes approximately 2–3 minutes. The free quiz is offered with a clearly disclosed FoundVital marketing subscription. After you answer seven questions, you\'ll see your result right away and receive it by email.', 'fvr' ); ?></p>
							<?php
							if ( $capture ) {
								fvr_signup_privacy_note( __( 'Follow-up education continues while you\'re subscribed. Your result is an educational reflection, not medical advice, diagnosis or treatment recommendations.', 'fvr' ) );
							}
							?>
						</form>
					</div>
					<p class="fineprint split__disclaimer"><?php esc_html_e( 'This quiz is for education only and does not provide medical advice, diagnosis, or treatment.', 'fvr' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
