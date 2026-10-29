<?php
/**
 * Template Name: Reset – Quiz
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$questions = fvr_get_quiz_questions();
	$total     = count( $questions );
	$capture   = 'off' !== fvr_opt( 'quiz_capture', 'required' );
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'eyebrow'  => 'Free quiz · about 2 minutes',
			'headline' => 'What\'s Getting in the Way of a Health Routine That Works for You?',
			'intro'    => 'A short, educational quiz to point you toward a helpful next step. It isn\'t a diagnosis or medical advice.',
		)
	);
	?>
	<section class="section section--tight" id="quiz">
		<div class="container container--narrow">
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
				<?php if ( ! fvr_opt( 'quiz_live' ) ) : ?>
					<div class="alert alert--warn"><strong><?php esc_html_e( 'Editor preview.', 'fvr' ); ?></strong> <?php esc_html_e( 'The quiz is not live for the public yet, and the questions are sample content awaiting client approval.', 'fvr' ); ?></div>
				<?php endif; ?>

				<form class="quiz" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-total="<?php echo esc_attr( (string) ( $total + ( $capture ? 1 : 0 ) ) ); ?>" data-questions="<?php echo esc_attr( (string) $total ); ?>">
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

					<?php if ( $capture ) : ?>
						<fieldset class="quiz__step quiz__step--final" data-step="<?php echo esc_attr( (string) ( $total + 1 ) ); ?>">
							<legend><span class="quiz__num"><?php esc_html_e( 'Last step', 'fvr' ); ?></span><?php esc_html_e( 'Where should we send your result?', 'fvr' ); ?></legend>
							<p class="muted"><?php esc_html_e( 'Enter your details to see your result. We\'ll also email it to you with tips matched to your result. We only record which result you received, never your individual answers.', 'fvr' ); ?></p>
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
								<label for="quiz-co"><?php esc_html_e( 'Yes, email me my result and occasional education and updates from FoundVital. I can unsubscribe at any time.', 'fvr' ); ?> <span class="req" aria-hidden="true">*</span></label>
							</div>
						</fieldset>
					<?php endif; ?>

					<div class="quiz__nav">
						<button type="button" class="btn btn--ghost" data-quiz-back hidden><span><?php esc_html_e( 'Back', 'fvr' ); ?></span></button>
						<button type="button" class="btn btn--primary" data-quiz-next hidden><span><?php esc_html_e( 'Next', 'fvr' ); ?></span><?php echo fvr_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
						<button type="submit" class="btn btn--primary" data-quiz-submit><span><?php esc_html_e( 'See My Result', 'fvr' ); ?></span><?php echo fvr_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</div>
					<?php
					if ( $capture ) {
						fvr_signup_privacy_note();
					}
					?>
				</form>
			<?php endif; ?>
			<p class="fineprint"><?php esc_html_e( 'This quiz is for education only and does not provide medical advice, diagnosis, or treatment.', 'fvr' ); ?></p>
		</div>
	</section>
	<?php
endwhile;
get_footer();
