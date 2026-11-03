<?php
/**
 * Quiz: question loading, server-side scoring, result routing, required Kit capture.
 * Scoring is intentionally simple (one point per answer toward A/B/C) and educational only.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Result definitions: key → slug, label, tag setting. */
function fvr_quiz_results(): array {
	return array(
		'A' => array( 'slug' => 'foundations', 'label' => 'Build Your Foundations', 'tag' => 'kit_tag_quiz_a' ),
		'B' => array( 'slug' => 'routine', 'label' => 'Create a Workable Routine', 'tag' => 'kit_tag_quiz_b' ),
		'C' => array( 'slug' => 'coaching', 'label' => 'Explore Structured Coaching', 'tag' => 'kit_tag_quiz_c' ),
	);
}

/**
 * Published questions with parsed answers.
 *
 * @return array<int, array{q:string, answers: array<int, array{text:string,key:string}>}>
 */
function fvr_get_quiz_questions(): array {
	$posts = get_posts(
		array(
			'post_type'      => 'fvr_quiz_q',
			'post_status'    => 'publish',
			'posts_per_page' => 30,
			'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
	$out = array();
	foreach ( $posts as $p ) {
		$answers = array();
		foreach ( fvr_lines( get_post_meta( $p->ID, '_fvr_answers', true ) ) as $line ) {
			$pos = strrpos( $line, '|' );
			if ( false === $pos ) {
				continue;
			}
			$key = strtoupper( trim( substr( $line, $pos + 1 ) ) );
			$txt = trim( substr( $line, 0, $pos ) );
			if ( '' !== $txt && isset( fvr_quiz_results()[ $key ] ) ) {
				$answers[] = array( 'text' => $txt, 'key' => $key );
			}
		}
		if ( count( $answers ) >= 2 ) {
			$out[ $p->ID ] = array( 'q' => get_the_title( $p ), 'answers' => $answers );
		}
	}
	return $out;
}

/**
 * Robin's routing: most answers wins. On a tie, use the Q7 answer if it is one of the tied results
 * (the last question if there are fewer than 7), then the Q1 answer, then Foundations, Routine, Coaching.
 *
 * @param array<string,int> $tally A/B/C counts.
 * @param string[]          $picks Result key chosen in each question, in order.
 */
function fvr_quiz_winner( array $tally, array $picks ): string {
	$max  = max( $tally );
	$tied = array_keys( array_filter( $tally, static fn( $n ) => $n === $max ) );
	if ( 1 === count( $tied ) ) {
		return $tied[0];
	}
	$q7 = $picks[ min( 6, count( $picks ) - 1 ) ] ?? '';
	foreach ( array( $q7, $picks[0] ?? '' ) as $pick ) {
		if ( in_array( $pick, $tied, true ) ) {
			return $pick;
		}
	}
	foreach ( array( 'A', 'B', 'C' ) as $key ) {
		if ( in_array( $key, $tied, true ) ) {
			return $key;
		}
	}
	return 'A';
}

/** Public visibility gate: live flag, or an editor previewing. */
function fvr_quiz_visible(): bool {
	return (bool) fvr_opt( 'quiz_live' ) || current_user_can( 'edit_pages' );
}

add_action( 'admin_post_nopriv_fvr_quiz', 'fvr_handle_quiz' );
add_action( 'admin_post_fvr_quiz', 'fvr_handle_quiz' );

function fvr_handle_quiz(): void {
	$nonce = isset( $_POST['_fvr_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_fvr_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'fvr_quiz' ) ) {
		fvr_redirect_error( 'nonce', 'quiz' );
	}
	$spam = fvr_spam_check( 'quiz' );
	if ( $spam ) {
		fvr_redirect_error( $spam, 'quiz' );
	}

	$questions = fvr_get_quiz_questions();
	$given     = isset( $_POST['fvr_a'] ) && is_array( $_POST['fvr_a'] ) ? array_map( 'absint', wp_unslash( $_POST['fvr_a'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( ! $questions ) {
		fvr_redirect_error( 'invalid', 'quiz' );
	}

	$tally = array( 'A' => 0, 'B' => 0, 'C' => 0 );
	$picks = array(); // Result key chosen in each question, in question order.
	foreach ( $questions as $qid => $q ) {
		if ( ! isset( $given[ $qid ] ) || ! isset( $q['answers'][ $given[ $qid ] ] ) ) {
			fvr_redirect_error( 'quizmiss', 'quiz' );
		}
		$picks[] = $q['answers'][ $given[ $qid ] ]['key'];
		++$tally[ end( $picks ) ];
	}

	$result = fvr_quiz_results()[ fvr_quiz_winner( $tally, $picks ) ];

	// Required email step: the result is only shown once first name, email, state and consent are given.
	// Only the result category is sent to Kit (as a tag), never answers. A Kit outage still shows the result.
	$sub_flag = 'none';
	if ( 'off' !== fvr_opt( 'quiz_capture', 'required' ) ) {
		$first = isset( $_POST['first_name'] ) ? mb_substr( trim( sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) ), 0, 60 ) : '';
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$state = fvr_posted_state();
		if ( '' === $first ) {
			fvr_redirect_error( 'name', 'quiz' );
		}
		if ( ! is_email( $email ) ) {
			fvr_redirect_error( 'email', 'quiz' );
		}
		if ( '' === $state ) {
			fvr_redirect_error( 'state', 'quiz' );
		}
		if ( empty( $_POST['consent'] ) ) {
			fvr_redirect_error( 'consent', 'quiz' );
		}
		$r        = fvr_kit_subscribe(
			$email,
			$first,
			(int) fvr_opt( 'kit_form_quiz' ),
			array( (int) fvr_opt( 'kit_tag_quiz_any' ), (int) fvr_opt( $result['tag'] ) ),
			array( 'state' => $state )
		);
		$sub_flag = is_wp_error( $r ) ? 'failed' : 'ok';
	}

	fvr_set_success_cookie( 'quiz' );
	wp_safe_redirect( add_query_arg( 'fvr_sub', $sub_flag, fvr_page_url( 'results/' . $result['slug'] ) ) );
	exit;
}
