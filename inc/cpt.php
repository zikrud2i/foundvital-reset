<?php
/**
 * Custom post types: FAQ entries and quiz questions (no repeater/Pro dependency).
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	static function () {
		register_post_type(
			'fvr_faq',
			array(
				'labels'          => array( 'name' => 'FAQ', 'singular_name' => 'FAQ entry', 'add_new_item' => 'Add FAQ entry', 'edit_item' => 'Edit FAQ entry', 'menu_name' => 'Reset FAQ' ),
				'public'          => false,
				'show_ui'         => true,
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-editor-help',
				'menu_position'   => 21,
				'supports'        => array( 'title', 'editor', 'page-attributes' ),
				'capability_type' => 'page',
				'has_archive'     => false,
				'rewrite'         => false,
			)
		);
		register_post_type(
			'fvr_quiz_q',
			array(
				'labels'          => array( 'name' => 'Quiz questions', 'singular_name' => 'Quiz question', 'add_new_item' => 'Add quiz question', 'edit_item' => 'Edit quiz question', 'menu_name' => 'Reset Quiz' ),
				'public'          => false,
				'show_ui'         => true,
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-forms',
				'menu_position'   => 22,
				'supports'        => array( 'title', 'page-attributes' ),
				'capability_type' => 'page',
				'has_archive'     => false,
				'rewrite'         => false,
			)
		);
	}
);

add_action(
	'add_meta_boxes',
	static function () {
		add_meta_box(
			'fvr_quiz_answers',
			__( 'Answers & routing', 'fvr' ),
			static function ( $post ) {
				wp_nonce_field( 'fvr_quiz_answers', 'fvr_quiz_nonce' );
				$val = (string) get_post_meta( $post->ID, '_fvr_answers', true );
				echo '<p>' . esc_html__( 'One answer per line in the format:  Answer text | A   (A, B or C = the result that answer points to).', 'fvr' ) . '</p>';
				echo '<p><strong>A</strong> = Build Your Foundations &nbsp; <strong>B</strong> = Create a Workable Routine &nbsp; <strong>C</strong> = Explore Structured Coaching</p>';
				echo '<textarea name="fvr_answers" rows="7" style="width:100%">' . esc_textarea( $val ) . '</textarea>';
				echo '<p class="description">' . esc_html__( 'Use the "Order" box (Page Attributes) to sort questions. The highest-scoring result wins; ties use the tie-break set in Reset Settings > Quiz. This is an educational routing tool, not a clinical assessment. Do not ask for symptoms, diagnoses or other sensitive health details.', 'fvr' ) . '</p>';
			},
			'fvr_quiz_q',
			'normal',
			'high'
		);
	}
);

add_action(
	'save_post_fvr_quiz_q',
	static function ( $post_id ) {
		if ( ! isset( $_POST['fvr_quiz_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fvr_quiz_nonce'] ) ), 'fvr_quiz_answers' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$raw = isset( $_POST['fvr_answers'] ) ? sanitize_textarea_field( wp_unslash( $_POST['fvr_answers'] ) ) : '';
		update_post_meta( $post_id, '_fvr_answers', $raw );
	}
);

/** Admin notice: pending FAQ drafts and sample quiz warning. */
add_action(
	'admin_notices',
	static function () {
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, array( 'fvr_faq', 'fvr_quiz_q' ), true ) ) {
			return;
		}
		if ( 'fvr_faq' === $screen->post_type ) {
			echo '<div class="notice notice-info"><p>Seeded FAQ entries marked <strong>[CONFIRM]</strong> are saved as <em>drafts</em> and stay hidden until you replace the placeholder with approved wording and publish them.</p></div>';
		} else {
			echo '<div class="notice notice-warning"><p>The seeded quiz questions are <strong>sample content pending client approval</strong>. The quiz stays hidden from the public until you tick “Quiz is live” in Reset Settings &gt; Quiz.</p></div>';
		}
	}
);
