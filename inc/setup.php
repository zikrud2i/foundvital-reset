<?php
/**
 * One-click setup: creates the page hierarchy, assigns templates, sets the static front page,
 * permalinks, FAQ drafts and sample quiz questions. Idempotent – never overwrites existing content.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', 'fvr_run_setup' );

/** Pages added after launch: created once, the first time an admin loads wp-admin after a theme update. */
add_action(
	'admin_init',
	static function () {
		if ( (int) get_option( 'fvr_pages_rev' ) >= 1 || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		fvr_ensure_page( 'assessment', 'Your Reset Starting Point', 'page-templates/assessment.php' );
		update_option( 'fvr_pages_rev', 1 );
	}
);

add_action(
	'admin_post_fvr_run_setup',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'fvr' ), 403 );
		}
		check_admin_referer( 'fvr_run_setup' );
		fvr_run_setup();
		wp_safe_redirect( add_query_arg( array( 'page' => 'fvr-settings', 'fvr_setup' => 1 ), admin_url( 'admin.php' ) ) );
		exit;
	}
);

/** Create a page at a path if it doesn't already exist. Returns page ID. */
function fvr_ensure_page( string $path, string $title, string $template = '', string $status = 'publish', string $content = '' ): int {
	$existing = get_page_by_path( $path );
	if ( $existing ) {
		return (int) $existing->ID;
	}
	$parts  = explode( '/', $path );
	$slug   = array_pop( $parts );
	$parent = $parts ? (int) get_page_by_path( implode( '/', $parts ) )->ID : 0;
	$id     = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => $status,
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_parent'  => $parent,
			'post_content' => $content,
		)
	);
	if ( $id && ! is_wp_error( $id ) && $template ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	return is_wp_error( $id ) ? 0 : (int) $id;
}

function fvr_run_setup(): void {
	if ( false === get_option( 'fvr_settings' ) ) {
		add_option( 'fvr_settings', array(), '', false );
	}

	// Permalinks: /%postname%/ (required for the nested /results/… and /thank-you/… routes).
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}

	$legal = '<p><em>Placeholder: the client must supply or approve the legal text before this page is published.</em></p>';
	$T     = 'page-templates/';

	// Home.
	$home = fvr_ensure_page( 'reset-home', 'FoundVital Reset', '' );
	if ( $home && ( ! (int) get_option( 'page_on_front' ) || 'page' !== get_option( 'show_on_front' ) ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}

	fvr_ensure_page( 'checklist', 'Seven Questions to Ask Before You Change Your Midlife Health Routine', $T . 'checklist.php' );
	fvr_ensure_page( 'quiz', 'What\'s Getting in the Way of a Health Routine That Works for You?', $T . 'quiz.php' );
	fvr_ensure_page( 'results', 'Quiz Results', '' );
	fvr_ensure_page( 'results/foundations', 'Build Your Foundations', $T . 'quiz-result.php' );
	fvr_ensure_page( 'results/routine', 'Create a Workable Routine', $T . 'quiz-result.php' );
	fvr_ensure_page( 'results/coaching', 'Explore Structured Coaching', $T . 'quiz-result.php' );
	fvr_ensure_page( 'masterclass', 'The Midlife Health Reset Masterclass', $T . 'masterclass.php' );
	fvr_ensure_page( 'fit-call', 'Book a Reset Fit Conversation', $T . 'fit-call.php' );
	fvr_ensure_page( 'enroll', 'Enroll in FoundVital Reset', $T . 'enroll.php' );
	fvr_ensure_page( 'thank-you', 'Thank You', '' );
	fvr_ensure_page( 'thank-you/checklist', 'Your Checklist', $T . 'thank-you.php' );
	fvr_ensure_page( 'thank-you/masterclass', 'You\'re Registered', $T . 'thank-you.php' );
	fvr_ensure_page( 'thank-you/fit-call', 'Your Fit Conversation', $T . 'thank-you.php' );
	fvr_ensure_page( 'thank-you/enrollment', 'Enrollment', $T . 'thank-you.php' );
	fvr_ensure_page( 'assessment', 'Your Reset Starting Point', $T . 'assessment.php' );
	fvr_ensure_page( 'clarity-coaching', 'FoundVital Clarity Coaching Session', $T . 'clarity-coaching.php', 'draft' );

	$privacy = fvr_ensure_page( 'privacy-policy', 'Privacy Policy', '', 'draft', $legal );
	fvr_ensure_page( 'terms', 'Terms of Use', '', 'draft', $legal );
	if ( $privacy && ! (int) get_option( 'wp_page_for_privacy_policy' ) ) {
		update_option( 'wp_page_for_privacy_policy', $privacy );
	}

	fvr_seed_faqs();
	fvr_seed_quiz();

	flush_rewrite_rules();
}

/** Redirect the container pages /results and /thank-you somewhere useful. */
add_action(
	'template_redirect',
	static function () {
		if ( ! is_page() ) {
			return;
		}
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( 'results' === $slug && ! get_post_field( 'post_parent', get_queried_object_id() ) ) {
			wp_safe_redirect( fvr_page_url( 'quiz' ), 302 );
			exit;
		}
		if ( 'thank-you' === $slug && ! get_post_field( 'post_parent', get_queried_object_id() ) ) {
			wp_safe_redirect( home_url( '/' ), 302 );
			exit;
		}
	}
);

/**
 * FAQ seed. Answers that depend on unconfirmed program details are saved as DRAFTS with a
 * [CONFIRM] marker so nothing unapproved is ever published.
 */
function fvr_seed_faqs(): void {
	if ( get_posts( array( 'post_type' => 'fvr_faq', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) ) ) {
		return;
	}
	$items = array(
		array( 'How much weekly time should I allow?', '[CONFIRM] Enter the approved weekly time estimate for self-paced learning, strength sessions and check-ins.', 'draft' ),
		array( 'How do coaching check-ins work?', '[CONFIRM] Enter the confirmed check-in frequency, format (video/phone/etc.), who leads them, and what is reviewed. Do not imply unlimited access.', 'draft' ),
		array( 'What support is available between sessions?', '[CONFIRM] Enter exactly what is available between check-ins (e.g. community, messaging, response times). Do not imply concierge access.', 'draft' ),
		array( 'Do I need a gym?', '[CONFIRM] Enter the approved equipment guidance for the strength and movement portions of Reset.', 'draft' ),
		array( 'Are labs or medications included?', 'No. Lab testing, medication, and other clinical services are separate from Reset. Reset is coaching and education. Medical evaluation, prescribing, and clinical lab interpretation are handled by separate licensed providers.', 'publish' ),
		array( 'What happens after week 12?', '[CONFIRM] Describe what participants keep access to, and the optional FoundVital Sustain™ membership with its approved terms.', 'draft' ),
	);
	foreach ( $items as $i => $it ) {
		wp_insert_post(
			array(
				'post_type'    => 'fvr_faq',
				'post_status'  => $it[2],
				'post_title'   => $it[0],
				'post_content' => $it[1],
				'menu_order'   => $i + 1,
			)
		);
	}
}

/**
 * The seven quiz questions from Tara's reviewed copy (October 2026). Each answer ends in "| A", "| B" or "| C":
 * A = Build Your Foundations, B = Create a Workable Routine, C = Explore Structured Coaching.
 *
 * @return array<int, array{0:string,1:string}>
 */
function fvr_quiz_default_questions(): array {
	return array(
		array(
			'When you think about your health routines, what feels least clear?',
			"Which of the many things I've read deserve attention first. | A\nHow to make the habits I value fit into a normal week. | B\nI've made an effort, but would value thoughtful guidance in reviewing whether my approach still fits my goals. | C",
		),
		array(
			'Think of a health tip you\'ve saved or read recently. What usually happens next?',
			"I'm not sure whether it should be a priority, so it sits in my saved posts. | A\nI understand the idea, but my schedule gets in the way of doing it. | B\nI try ideas, but would appreciate someone to help me look at what feels useful and what needs adjusting. | C",
		),
		array(
			'When your schedule gets unexpectedly busy, which challenge is most familiar?',
			"I can't tell which habits matter enough to protect. | A\nMy routines become hard to repeat because I haven't planned a flexible option. | B\nI keep doing what I can, but a scheduled check-in would help me reflect and choose the next step. | C",
		),
		array(
			'When you think about nutrition, strength, sleep, and recovery together, which sounds most like you?',
			"There are a lot of recommendations and I'm unsure where to start. | A\nI have priorities, but they compete for the same time and attention. | B\nI have some consistent practices and would value knowledgeable support in thinking about how they fit together. | C",
		),
		array(
			'If you had 15 minutes to make next week feel more manageable, what would you do?',
			"Choose one useful focus instead of adding several new goals. | A\nPut one existing habit on the calendar and plan a backup for a busy day. | B\nTalk through what has been working with someone who can help me set realistic goals and review them. | C",
		),
		array(
			'Which description best matches where you are now?',
			"I've collected a lot of ideas and want to simplify before doing more. | A\nI know some habits I want, but staying consistent is difficult. | B\nI've established some routines and think structured accountability and scheduled guidance would help. | C",
		),
		array(
			'When you think about taking a next step, what type of information or guidance would feel most useful?',
			"A clear, practical introduction to foundations and which topic to focus on first. | A\nA clear explanation of how to put habits together using a structured, four-phase approach. | B\nA chance to understand what coaching with an experienced professional involves before deciding whether it's right for me. | C",
		),
	);
}

/** Seed the quiz questions on a fresh install (never touches existing questions). */
function fvr_seed_quiz(): void {
	if ( get_posts( array( 'post_type' => 'fvr_quiz_q', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) ) ) {
		return;
	}
	foreach ( fvr_quiz_default_questions() as $i => $q ) {
		$id = wp_insert_post( array( 'post_type' => 'fvr_quiz_q', 'post_status' => 'publish', 'post_title' => $q[0], 'menu_order' => $i + 1 ) );
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_fvr_answers', $q[1] );
		}
	}
}
