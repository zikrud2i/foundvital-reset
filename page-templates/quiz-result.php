<?php
/**
 * Template Name: Reset – Quiz Result
 * Result kind is derived from the page slug: foundations (A), routine (B), coaching (C).
 *
 * One shared structure (Tara review copy, October 2026): welcome → "Your personal starting point"
 * (the only part that changes per result) → What is Reset? → four phases → Meet Tara → What you can
 * expect (incl. investment) → Explore Reset.
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$slug = get_post_field( 'post_name', get_the_ID() );

	$pdf_url = fvr_checklist_pdf_url() ? fvr_checklist_pdf_url() : fvr_page_url( 'checklist' );
	$home    = home_url( '/' );

	/*
	 * Result A is Tara's approved sample. B and C are drafted from the one-line briefs in her copy document
	 * (implementation / busy-week planning; value and limits of coaching) and need her review.
	 */
	$defaults = array(
		'foundations' => array(
			'key'      => 'A',
			'label'    => 'Build Your Foundations',
			'lead'     => 'Your answers suggest you may have plenty of nutrition, strength, sleep, and recovery ideas, but choosing where to start is the harder part. Think of the recipe you\'ve saved, the workout you\'ve meant to try, and the sleep advice you keep hearing. You don\'t need to act on all of them this week. Start by choosing one topic to understand better and noticing one thing you\'re already doing well.',
			'ex_title' => 'Try this in five minutes',
			'ex_steps' => array( 'Write down three health topics competing for your attention.', 'Circle one topic you\'d like to learn more about this week.', 'Name one habit you\'re already doing that you\'d like to keep.' ),
			'ex_fill'  => 'One topic I want to understand better is __________. One habit I want to keep is __________.',
			'invite'   => 'If you\'d like more structure around these priorities, you can learn about FoundVital Reset™, a 12-week coaching program guided by a PA with 16 years of clinical experience, with practical education and scheduled guidance.',
			'primary'  => array( 'Explore FoundVital Reset™', $home, 'cta_explore_reset_result' ),
			'second'   => array( 'Get the Free Printable Checklist', $pdf_url, 'checklist_download_click' ),
		),
		'routine'     => array(
			'key'      => 'B',
			'label'    => 'Create a Workable Routine',
			'lead'     => 'Your answers suggest you already know a good deal about what you\'d like to do. The harder part is making it repeatable once the calendar fills up. A routine that works in an ordinary week and bends in a busy one is often easier to keep than a perfect plan. Think about one habit you value, and what it could look like on both kinds of days.',
			'ex_title' => 'Plan one habit for two kinds of weeks',
			'ex_steps' => array( 'Name one habit you want to keep.', 'Describe how it fits into an ordinary week: when, where, and for how long.', 'Describe a smaller backup version for a busy week.' ),
			'ex_fill'  => 'My ordinary-week plan is __________. My busy-week backup is __________.',
			'invite'   => 'If you\'d like a structured way to build routines like this, you can learn about FoundVital Reset™, a 12-week coaching program guided by a PA with 16 years of clinical experience, with practical education and scheduled guidance.',
			'primary'  => array( 'Explore FoundVital Reset™', $home, 'cta_explore_reset_result' ),
			'second'   => null,
		),
		'coaching'    => array(
			'key'      => 'C',
			'label'    => 'Explore Structured Coaching',
			'lead'     => 'Your answers suggest you\'ve already put real effort in and would value an experienced second set of eyes. Coaching can help you review what\'s working, set realistic goals, and keep a plan moving. It isn\'t a quick fix, and it doesn\'t replace care from your licensed healthcare professional. It tends to work best when you\'re ready to reflect, try things, and check in.',
			'ex_title' => 'Questions to bring to a conversation',
			'ex_steps' => array( 'What do I most want help with over the next 12 weeks?', 'How much time could I realistically set aside each week?', 'What kind of support and accountability helps me follow through?' ),
			'ex_fill'  => 'My top goal is __________. The time I can set aside each week is __________.',
			'invite'   => 'A Reset Fit Conversation gives you time to ask questions and decide whether FoundVital Reset™, a 12-week coaching program guided by a PA with 16 years of clinical experience, matches what you\'re looking for.',
			'primary'  => array( 'Book a Reset Fit Conversation', fvr_page_url( 'fit-call' ), 'cta_fit_call_result' ),
			'second'   => array( 'Explore the Reset program', $home, 'cta_explore_reset_result' ),
		),
	);
	$d    = $defaults[ $slug ] ?? $defaults['foundations'];
	$sub  = isset( $_GET['fvr_sub'] ) ? sanitize_key( wp_unslash( $_GET['fvr_sub'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$tara = fvr_founder_photo( 'thumbnail', array( 'class' => 'result-tara__img', 'loading' => 'lazy' ) );
	?>
	<section class="page-hero">
		<div class="container container--narrow text-center" data-quiz-result="<?php echo esc_attr( $d['key'] ); ?>">
			<?php fvr_breadcrumbs(); ?>
			<p class="eyebrow"><?php echo esc_html( fvr_field( 'page_eyebrow', 'Your FoundVital Reset™ quiz result' ) ); ?></p>
			<h1><?php echo esc_html( fvr_field( 'page_headline', $d['label'] ) ); ?></h1>
			<p class="lead"><?php esc_html_e( 'Think of this as a starting point for reflection, not a verdict. Your answers point to one place you might begin, along with a simple exercise you can try on your own.', 'fvr' ); ?></p>
		</div>
	</section>

	<section class="section section--tight" aria-labelledby="start-title">
		<div class="container container--narrow">
			<?php if ( 'ok' === $sub ) : ?>
				<div class="alert alert--ok" role="status"><?php esc_html_e( 'Thanks. You\'re on the list, and we\'ll email you a link to this result.', 'fvr' ); ?></div>
			<?php elseif ( 'failed' === $sub ) : ?>
				<div class="alert alert--error" role="status"><?php esc_html_e( 'We couldn\'t add your email just now. Your result is below. You can also join via the free checklist page.', 'fvr' ); ?></div>
			<?php elseif ( 'invalid' === $sub ) : ?>
				<div class="alert alert--error" role="status"><?php esc_html_e( 'We couldn\'t sign you up because a detail was missing (first name, email, state or consent), so nothing was subscribed. Your result is below.', 'fvr' ); ?></div>
			<?php endif; ?>

			<div class="panel">
				<p class="eyebrow"><?php esc_html_e( 'Your personal starting point', 'fvr' ); ?></p>
				<h2 id="start-title"><?php echo esc_html( $d['label'] ); ?></h2>
				<p><?php echo esc_html( fvr_field( 'page_intro', $d['lead'] ) ); ?></p>

				<div class="exercise">
					<h3><?php echo esc_html( $d['ex_title'] ); ?></h3>
					<ol>
						<?php foreach ( $d['ex_steps'] as $step ) : ?>
							<li><?php echo esc_html( $step ); ?></li>
						<?php endforeach; ?>
					</ol>
					<p class="exercise__fill"><?php echo esc_html( $d['ex_fill'] ); ?></p>
				</div>

				<p><?php echo esc_html( $d['invite'] ); ?></p>
				<div class="btn-row">
					<?php fvr_btn( $d['primary'][0], $d['primary'][1], 'primary', $d['primary'][2] ); ?>
					<?php if ( $d['second'] ) : ?>
						<?php fvr_btn( $d['second'][0], $d['second'][1], 'ghost', $d['second'][2] ); ?>
					<?php endif; ?>
				</div>
			</div>
			<p class="fineprint"><?php esc_html_e( 'This result is an educational reflection based on your preferences. It is not a validated score, a clinical judgment, medical advice, or a treatment recommendation.', 'fvr' ); ?></p>
		</div>
	</section>

	<section class="section section--sand" aria-labelledby="what-title">
		<div class="container container--narrow text-center reveal">
			<p class="eyebrow"><?php esc_html_e( 'About the program', 'fvr' ); ?></p>
			<h2 id="what-title"><?php esc_html_e( 'What is FoundVital Reset™?', 'fvr' ); ?></h2>
			<p>FoundVital Reset™ is a 12-week coaching program, guided by a PA with 16 years of clinical experience, designed to help women bring nutrition, strength, sleep, and recovery into a more workable approach. It combines practical learning with scheduled guidance and accountability from Tara, without another rigid set of rules.</p>
		</div>
	</section>

	<?php get_template_part( 'template-parts/phases' ); ?>

	<section class="section" aria-labelledby="tara-title">
		<div class="container container--narrow">
			<div class="result-tara reveal">
				<?php echo $tara; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Meet Tara', 'fvr' ); ?></p>
					<h2 id="tara-title" class="h3"><?php esc_html_e( 'Hi, I\'m Tara Schmidt, PA-C, DipACLM.', 'fvr' ); ?></h2>
					<p>My 16 years of clinical experience as a physician assistant and my training in lifestyle medicine have shaped how I think about health. Knowing what to do and figuring out how to make it work in everyday life are two different things. I created Reset to bring practical education and real guidance together, so you have a clearer way to work toward your goals.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--sand" aria-labelledby="expect-title">
		<div class="container container--narrow">
			<div class="section__head reveal">
				<p class="eyebrow"><?php esc_html_e( 'The details', 'fvr' ); ?></p>
				<h2 id="expect-title"><?php esc_html_e( 'What you can expect', 'fvr' ); ?></h2>
			</div>
			<div class="panel reveal">
				<ul class="check-list">
					<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'A largely self-paced 12-week coaching experience with weekly educational lessons.', 'fvr' ); ?></span></li>
					<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'One 60-minute individual onboarding call and five 30-minute individual coaching check-ins.', 'fvr' ); ?></span></li>
					<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'A private Circle community, a Welcome Kit, and other included resources.', 'fvr' ); ?></span></li>
					<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Typical participation is about 3–4 hours a week.', 'fvr' ); ?></span></li>
				</ul>
			</div>
			<?php get_template_part( 'template-parts/pricing', null, array( 'heading_level' => 3 ) ); ?>
		</div>
	</section>

	<section class="cta-band" aria-labelledby="explore-title">
		<div class="container container--narrow text-center reveal">
			<h2 id="explore-title"><?php esc_html_e( 'Explore Reset', 'fvr' ); ?></h2>
			<p><?php esc_html_e( 'Ready to learn more? Take a look at the full Reset program, including how it works, what is included, pricing, and how to book a Reset Fit Conversation.', 'fvr' ); ?></p>
			<div class="btn-row btn-row--center">
				<?php fvr_btn( __( 'See Program Details & Investment', 'fvr' ), home_url( '/#fit-and-investment' ), 'gold', 'cta_program_details_result' ); ?>
				<?php fvr_btn( __( 'Retake the quiz', 'fvr' ), fvr_page_url( 'quiz' ), 'light' ); ?>
			</div>
		</div>
	</section>
	<?php
endwhile;
get_footer();
