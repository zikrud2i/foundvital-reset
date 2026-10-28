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

/** Sample quiz questions – clearly sample content pending client approval. */
function fvr_seed_quiz(): void {
	if ( get_posts( array( 'post_type' => 'fvr_quiz_q', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) ) ) {
		return;
	}
	$qs = array(
		array( 'Which sounds most like where you are right now?', "I'm still getting the basics in place | A\nI know what to do, but keeping it consistent is the hard part | B\nI've been doing a lot and want more structured guidance | C" ),
		array( 'How would you describe your current routine?', "It changes from week to week and doesn't have much structure | A\nSome habits stick, others come and go | B\nMy routine is solid, but I'm not sure I'm prioritizing the right things | C" ),
		array( 'What kind of support sounds most helpful?', "Simple starting points I can use on my own | A\nPractical tools that turn what I know into repeatable habits | B\nScheduled coaching check-ins and accountability | C" ),
		array( 'How do you feel about working independently between check-ins?', "I'd like to begin with simple resources first | A\nComfortable, as long as I have practical tools | B\nComfortable, and I'd like guidance to adjust along the way | C" ),
		array( 'What would make the next 12 weeks feel worthwhile?', "Clarity on where to begin | A\nA routine that fits my real week | B\nA structured plan with someone to review it with | C" ),
	);
	foreach ( $qs as $i => $q ) {
		$id = wp_insert_post( array( 'post_type' => 'fvr_quiz_q', 'post_status' => 'publish', 'post_title' => $q[0], 'menu_order' => $i + 1 ) );
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_fvr_answers', $q[1] );
		}
	}
}
