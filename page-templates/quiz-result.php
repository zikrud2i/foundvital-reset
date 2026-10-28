<?php
/**
 * Template Name: Reset – Quiz Result
 * Result kind is derived from the page slug: foundations (A), routine (B), coaching (C).
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$slug = get_post_field( 'post_name', get_the_ID() );

	$defaults = array(
		'foundations' => array(
			'key'    => 'A',
			'label'  => 'Build Your Foundations',
			'intro'  => 'A good next step is to start with simple, practical foundations: lifestyle, nutrition, and mindset basics you can build on.',
			'points' => "Choose one or two simple starting priorities\nBuild everyday nutrition, sleep, and stress routines you can repeat\nLearn the \"why\" behind each habit so decisions feel clearer",
			'ctas'   => array( array( 'Get the Free Checklist', 'checklist', 'primary', 'cta_checklist_result' ) ),
			'soft'   => 'Prefer to talk it through? Book a fit conversation.',
		),
		'routine'     => array(
			'key'    => 'B',
			'label'  => 'Create a Workable Routine',
			'intro'  => 'You already know a lot. A good next step is connecting what you know with routines that hold up in a busy week.',
			'points' => "Turn what you already know into a short list of repeatable habits\nMatch your routine to your real schedule, not an ideal one\nSee how Reset's structure can help the habits stick",
			'ctas'   => array( array( 'Get the Free Checklist', 'checklist', 'primary', 'cta_checklist_result' ) ),
			'soft'   => 'Curious about structured support? Book a fit conversation.',
		),
		'coaching'    => array(
			'key'    => 'C',
			'label'  => 'Explore Structured Coaching',
			'intro'  => 'You may be ready for more structure and accountability. A fit conversation is a good way to see whether Reset matches what you\'re looking for.',
			'points' => "Scheduled, personalized coaching check-ins\nA 12-week path through nutrition, strength, recovery, and habits\nTime to ask questions and decide on your own timeline",
			'ctas'   => array( array( 'Book a Reset Fit Conversation', 'fit-call', 'primary', 'cta_fit_call_result' ), array( 'See How Reset Works', '', 'secondary', 'cta_how_it_works' ) ),
			'soft'   => 'Want to learn more first? Get the free checklist.',
		),
	);
	$d      = $defaults[ $slug ] ?? $defaults['foundations'];
	$points = fvr_lines( fvr_field( 'page_points', $d['points'] ) );
	$sub    = isset( $_GET['fvr_sub'] ) ? sanitize_key( wp_unslash( $_GET['fvr_sub'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<section class="page-hero">
		<div class="container container--narrow text-center" data-quiz-result="<?php echo esc_attr( $d['key'] ); ?>">
			<?php fvr_breadcrumbs(); ?>
			<p class="eyebrow"><?php echo esc_html( fvr_field( 'page_eyebrow', 'Your result' ) ); ?></p>
			<h1><?php echo esc_html( fvr_field( 'page_headline', $d['label'] ) ); ?></h1>
			<p class="lead"><?php echo esc_html( fvr_field( 'page_intro', $d['intro'] ) ); ?></p>
		</div>
	</section>

	<section class="section section--tight">
		<div class="container container--narrow">
			<?php if ( 'ok' === $sub ) : ?>
				<div class="alert alert--ok" role="status"><?php esc_html_e( 'Thanks. You\'re on the list, and we\'ll send helpful follow-up to your email.', 'fvr' ); ?></div>
			<?php elseif ( 'failed' === $sub ) : ?>
				<div class="alert alert--error" role="status"><?php esc_html_e( 'We couldn\'t add your email just now. Your result is below. You can also join via the free checklist page.', 'fvr' ); ?></div>
			<?php elseif ( 'invalid' === $sub ) : ?>
				<div class="alert alert--error" role="status"><?php esc_html_e( 'We couldn\'t use that email address or consent selection, so nothing was subscribed. Your result is below.', 'fvr' ); ?></div>
			<?php endif; ?>

			<div class="panel">
				<h2 class="h3"><?php echo esc_html( fvr_field( 'page_points_title', 'Good places to focus' ) ); ?></h2>
				<ul class="check-list">
					<?php foreach ( $points as $pt ) : ?>
						<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $pt ); ?></span></li>
					<?php endforeach; ?>
				</ul>
				<div class="btn-row">
					<?php
					foreach ( $d['ctas'] as $c ) {
						$url = $c[1] ? fvr_page_url( $c[1] ) : home_url( '/#how-reset-works' );
						fvr_btn( $c[0], $url, $c[2], $c[3] );
					}
					?>
				</div>
				<p class="muted mt"><?php echo esc_html( $d['soft'] ); ?> <a href="<?php echo esc_url( 'coaching' === $slug ? fvr_page_url( 'checklist' ) : fvr_page_url( 'fit-call' ) ); ?>"><?php esc_html_e( 'Learn more', 'fvr' ); ?></a></p>
			</div>

			<p class="fineprint"><?php esc_html_e( 'This quiz is educational and not a diagnosis or medical advice. Results simply suggest a helpful next step.', 'fvr' ); ?></p>
			<p class="text-center"><a href="<?php echo esc_url( fvr_page_url( 'quiz' ) ); ?>"><?php esc_html_e( 'Retake the quiz', 'fvr' ); ?></a></p>
		</div>
	</section>
	<?php
endwhile;
get_footer();
