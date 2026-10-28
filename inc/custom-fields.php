<?php
/**
 * Secure Custom Fields (SCF) field groups for per-page content.
 *
 * Only free-tier field types are used (text, textarea, wysiwyg, image, url), so no Pro
 * features (repeaters, options pages) are required. Templates read values through
 * fvr_field() which falls back to built-in copy from the client's messaging guide.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Build a compact field definition. */
function fvr_f( string $name, string $label, string $type = 'text', string $instr = '' ): array {
	return array(
		'key'          => 'field_fvr_' . $name,
		'name'         => $name,
		'label'        => $label,
		'type'         => $type,
		'instructions' => $instr,
		'rows'         => 'textarea' === $type ? 4 : null,
		'new_lines'    => 'textarea' === $type ? '' : null,
		'media_upload' => 'wysiwyg' === $type ? 0 : null,
		'return_format'=> 'image' === $type ? 'id' : null,
	);
}

add_action(
	'acf/init',
	static function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}
		$lines = 'One item per line.';

		$groups = array(
			'home'      => array(
				'title'    => 'Reset Homepage content',
				'location' => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ),
				'fields'   => array(
					fvr_f( 'hero_eyebrow', 'Hero: eyebrow' ),
					fvr_f( 'hero_headline', 'Hero: headline' ),
					fvr_f( 'hero_sub', 'Hero: supporting message', 'textarea' ),
					fvr_f( 'hero_points', 'Hero: highlight points', 'textarea', $lines ),
					fvr_f( 'hero_note', 'Hero: coaching vs. medical note', 'textarea' ),
					fvr_f( 'recog_heading', 'Recognition: heading' ),
					fvr_f( 'recog_p1', 'Recognition: paragraph 1', 'textarea' ),
					fvr_f( 'recog_p2', 'Recognition: paragraph 2', 'textarea' ),
					fvr_f( 'exp_heading', 'Experience: heading' ),
					fvr_f( 'exp1_text', 'Card 1 – Your Starting Point', 'textarea' ),
					fvr_f( 'exp2_text', 'Card 2 – Nutrition, Strength, Recovery', 'textarea' ),
					fvr_f( 'exp3_text', 'Card 3 – Coaching & Accountability', 'textarea' ),
					fvr_f( 'exp4_text', 'Card 4 – Tools & Community', 'textarea' ),
					fvr_f( 'phases_sample_image', 'Phases: authentic sample image', 'image', 'A real page from the Welcome Kit, Blueprint or three-commitment worksheet.' ),
					fvr_f( 'phases_sample_caption', 'Phases: sample caption' ),
					fvr_f( 'meet_heading', 'Meet Tara: heading' ),
					fvr_f( 'meet_p1', 'Meet Tara: background paragraph', 'textarea' ),
					fvr_f( 'meet_p2', 'Meet Tara: why Reset exists', 'textarea' ),
					fvr_f( 'meet_p3', 'Meet Tara: philosophy & role', 'textarea' ),
					fvr_f( 'meet_boundary', 'Meet Tara: coaching vs. medical note', 'textarea' ),
					fvr_f( 'fit_heading', 'Fit: heading' ),
					fvr_f( 'fit_good', 'Fit: who Reset suits', 'textarea', $lines ),
					fvr_f( 'fit_not', 'Fit: what Reset is not', 'textarea', $lines ),
					fvr_f( 'fit_included', 'Fit: what\'s included', 'textarea', $lines ),
					fvr_f( 'fit_weekly', 'Fit: weekly participation note', 'textarea', 'Only publish once the weekly time estimate is confirmed.' ),
					fvr_f( 'final_heading', 'Final invitation: heading' ),
					fvr_f( 'final_text', 'Final invitation: text', 'textarea' ),
				),
			),
			'pagehero'  => array(
				'title'    => 'Page header & intro',
				'location' => array(
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/checklist.php' ) ),
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/quiz.php' ) ),
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/masterclass.php' ) ),
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/fit-call.php' ) ),
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/clarity-coaching.php' ) ),
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/quiz-result.php' ) ),
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/thank-you.php' ) ),
					array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/enroll.php' ) ),
				),
				'fields'   => array(
					fvr_f( 'page_eyebrow', 'Eyebrow text' ),
					fvr_f( 'page_headline', 'Headline (blank = page title)' ),
					fvr_f( 'page_intro', 'Intro paragraph', 'textarea', 'Also used as the meta-description fallback if Rank Math is inactive.' ),
					fvr_f( 'page_points', 'Benefit / detail list', 'textarea', $lines ),
					fvr_f( 'page_points_title', 'Title for the list above' ),
					fvr_f( 'page_extra', 'Extra section text (before the CTA band)', 'textarea', 'Used by: checklist preview, fit-call "what to expect", masterclass "what you\'ll learn" – one item per line.' ),
					fvr_f( 'page_extra_title', 'Title for the extra section' ),
					fvr_f( 'page_social_image', 'Social/Open Graph image (optional, set in Rank Math too)', 'image' ),
				),
			),
		);

		foreach ( $groups as $key => $g ) {
			acf_add_local_field_group(
				array(
					'key'      => 'group_fvr_' . $key,
					'title'    => $g['title'],
					'fields'   => array_map(
						static function ( $f ) {
							return array_filter( $f, static fn( $v ) => null !== $v );
						},
						$g['fields']
					),
					'location' => $g['location'],
					'position' => 'normal',
					'style'    => 'default',
				)
			);
		}
	}
);

/** Gentle nudge when SCF is not active (content still renders from built-in copy). */
add_action(
	'admin_notices',
	static function () {
		if ( function_exists( 'acf_add_local_field_group' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && in_array( $screen->base, array( 'dashboard', 'edit', 'post', 'toplevel_page_fvr-settings' ), true ) ) {
			echo '<div class="notice notice-warning"><p><strong>FoundVital Reset:</strong> install and activate <em>Secure Custom Fields</em> to edit page copy (hero, intro, lists) from the page editor. The theme shows its built-in copy until then.</p></div>';
		}
	}
);
