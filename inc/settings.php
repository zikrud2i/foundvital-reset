<?php
/**
 * Reset Settings: all site-wide, client-editable configuration
 * (links, Kit, Stripe, pricing, founder, masterclass, tracking) plus a launch-readiness panel.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Field schema, grouped by tab. Types: text, url, email, textarea, checkbox, select, media, secret, id. */
function fvr_settings_schema(): array {
	return array(
		'links'     => array(
			'label'  => __( 'Links & checkout', 'fvr' ),
			'fields' => array(
				array( 'key' => 'main_site_url', 'label' => 'Main FoundVital site URL', 'type' => 'url', 'desc' => 'Used for the "back to FoundVital.com" links.' ),
				array( 'key' => 'calendly_url', 'label' => 'Calendly booking URL', 'type' => 'url', 'desc' => 'Fit conversation calendar (client-supplied link pre-filled; the ?month= parameter can be removed).' ),
				array( 'key' => 'calendly_embed', 'label' => 'Embed Calendly on /fit-call', 'type' => 'checkbox', 'desc' => 'Loads Calendly\'s inline widget (third-party script) once the visitor scrolls to it. The external link always remains as a fallback.' ),
				array( 'key' => 'calendly_redirect_on_book', 'label' => 'Redirect to thank-you page after booking', 'type' => 'checkbox', 'desc' => 'Uses Calendly\'s verified "event scheduled" browser event from the embed. If you don\'t embed, set the redirect inside Calendly instead (paid plans).' ),
				array( 'key' => 'stripe_reset_url', 'label' => 'Stripe Payment Link – Reset enrolment', 'type' => 'url', 'desc' => 'https://buy.stripe.com/… Leave blank until price & billing terms are approved. Shown only on /enroll.' ),
				array( 'key' => 'stripe_clarity_url', 'label' => 'Stripe Payment Link – Clarity Coaching ($100)', 'type' => 'url', 'desc' => 'FoundVital collects ONLY the coaching payment here.' ),
				array( 'key' => 'outfitmd_url', 'label' => 'OutfitMD affiliate shop URL ($99 testing)', 'type' => 'url', 'desc' => 'External purchase. FoundVital does not collect this payment.' ),
				array( 'key' => 'lab_page_url', 'label' => 'Clarity Coaching page URL (if hosted on foundvital.com)', 'type' => 'url', 'desc' => 'Architecture decision pending. Leave as the proposed foundvital.com URL, or point to this site\'s /clarity-coaching page.' ),
				array( 'key' => 'support_email', 'label' => 'Support / contact email', 'type' => 'email', 'desc' => 'Optional. Shown on confirmation pages when set.' ),
			),
		),
		'pricing'   => array(
			'label'  => __( 'Pricing', 'fvr' ),
			'fields' => array(
				array( 'key' => 'price_show', 'label' => 'Show pricing block', 'type' => 'checkbox', 'desc' => 'Leave OFF until the client approves the exact price and terms.' ),
				array( 'key' => 'price_text', 'label' => 'Price', 'type' => 'text', 'desc' => 'Exactly as approved, shown as the headline of the pricing card. No guessed amounts.' ),
				array( 'key' => 'price_terms', 'label' => 'Payment options & terms', 'type' => 'textarea', 'desc' => 'One item per line (payment options, full financial obligation, refund/program terms). Only enter approved terms.' ),
				array( 'key' => 'checkin_details', 'label' => 'Coaching check-in details', 'type' => 'textarea', 'desc' => 'Confirmed frequency, format, provider and between-check-in support. One item per line. Do not imply unlimited or concierge access.' ),
				array( 'key' => 'guarantee_text', 'label' => 'Completion Guarantee', 'type' => 'textarea', 'desc' => 'Shown under the price with the heading "Completion Guarantee". Hidden when blank.' ),
				array( 'key' => 'optimize_terms', 'label' => 'FoundVital Sustain™ membership terms', 'type' => 'textarea', 'desc' => 'Approved terms only. Hidden when blank.' ),
				array( 'key' => 'clarity_test_price', 'label' => 'Clarity: testing price (OutfitMD)', 'type' => 'text' ),
				array( 'key' => 'clarity_coach_price', 'label' => 'Clarity: coaching price (FoundVital Stripe)', 'type' => 'text' ),
				array( 'key' => 'clarity_total_price', 'label' => 'Clarity: combined total', 'type' => 'text' ),
			),
		),
		'founder'   => array(
			'label'  => __( 'Founder & brand', 'fvr' ),
			'fields' => array(
				array( 'key' => 'founder_name', 'label' => 'Name', 'type' => 'text', 'desc' => 'Add last name if approved.' ),
				array( 'key' => 'founder_credentials', 'label' => 'Credentials', 'type' => 'text', 'desc' => 'Comma-separated. Client-supplied: PA-C, DipACLM – confirm before launch.' ),
				array( 'key' => 'founder_experience', 'label' => 'Experience statement', 'type' => 'text', 'desc' => 'e.g. "approximately 12 years". Leave blank until verified – hidden when empty.' ),
				array( 'key' => 'founder_photo_id', 'label' => 'Founder portrait', 'type' => 'media', 'desc' => 'Approved professional photograph of Tara (portrait orientation works best).' ),
				array( 'key' => 'founder_video_url', 'label' => 'Founder video URL (optional)', 'type' => 'url', 'desc' => 'YouTube/Vimeo link or an .mp4 file. 45–60 seconds recommended.' ),
				array( 'key' => 'logo_id', 'label' => 'Logo override', 'type' => 'media', 'desc' => 'Blank = bundled FoundVital logo.' ),
				array( 'key' => 'cta_primary_label', 'label' => 'Primary CTA label', 'type' => 'text' ),
				array( 'key' => 'cta_secondary_label', 'label' => 'Secondary CTA label', 'type' => 'text' ),
				array( 'key' => 'footer_disclaimer', 'label' => 'Footer disclaimer', 'type' => 'textarea' ),
				array( 'key' => 'social_instagram', 'label' => 'Instagram URL', 'type' => 'url' ),
				array( 'key' => 'social_facebook', 'label' => 'Facebook URL', 'type' => 'url' ),
				array( 'key' => 'social_threads', 'label' => 'Threads URL', 'type' => 'url' ),
				array( 'key' => 'social_youtube', 'label' => 'YouTube URL', 'type' => 'url' ),
			),
		),
		'kit'       => array(
			'label'  => __( 'Kit & forms', 'fvr' ),
			'fields' => array(
				array( 'key' => 'kit_api_key', 'label' => 'Kit API key (v4)', 'type' => 'secret', 'desc' => 'Better: define FVR_KIT_API_KEY in wp-config.php (then this field is ignored). Stored server-side only, never printed. Type CLEAR to remove.' ),
				array( 'key' => 'kit_form_checklist', 'label' => 'Kit Form ID – checklist', 'type' => 'id' ),
				array( 'key' => 'kit_form_masterclass', 'label' => 'Kit Form ID – masterclass', 'type' => 'id' ),
				array( 'key' => 'kit_form_quiz', 'label' => 'Kit Form ID – quiz email capture', 'type' => 'id' ),
				array( 'key' => 'kit_tag_checklist', 'label' => 'Kit Tag ID – checklist', 'type' => 'id' ),
				array( 'key' => 'kit_tag_masterclass', 'label' => 'Kit Tag ID – masterclass registrant', 'type' => 'id' ),
				array( 'key' => 'kit_tag_quiz_any', 'label' => 'Kit Tag ID – quiz taker (any result)', 'type' => 'id' ),
				array( 'key' => 'kit_tag_quiz_a', 'label' => 'Kit Tag ID – Result A (Foundations)', 'type' => 'id' ),
				array( 'key' => 'kit_tag_quiz_b', 'label' => 'Kit Tag ID – Result B (Routine)', 'type' => 'id' ),
				array( 'key' => 'kit_tag_quiz_c', 'label' => 'Kit Tag ID – Result C (Coaching)', 'type' => 'id' ),
				array( 'key' => 'forminator_checklist_id', 'label' => 'Forminator form ID – checklist (optional)', 'type' => 'id', 'desc' => 'If set, Forminator replaces the built-in form. You then configure its Kit add-on and redirect inside Forminator.' ),
				array( 'key' => 'forminator_masterclass_id', 'label' => 'Forminator form ID – masterclass (optional)', 'type' => 'id' ),
				array( 'key' => 'turnstile_site_key', 'label' => 'Cloudflare Turnstile site key (optional)', 'type' => 'text', 'desc' => 'Extra spam protection for the built-in forms and quiz.' ),
				array( 'key' => 'turnstile_secret', 'label' => 'Cloudflare Turnstile secret key', 'type' => 'secret' ),
			),
		),
		'checklist' => array(
			'label'  => __( 'Checklist & masterclass', 'fvr' ),
			'fields' => array(
				array( 'key' => 'checklist_url', 'label' => 'Checklist download URL', 'type' => 'url', 'desc' => 'PDF in the Media Library or hosted file. Blank = delivery by email (Kit).' ),
				array( 'key' => 'mc_title', 'label' => 'Masterclass title', 'type' => 'text', 'desc' => 'Blank uses the title from the messaging guide.' ),
				array( 'key' => 'mc_datetime', 'label' => 'Date & time (display text)', 'type' => 'text', 'desc' => 'e.g. "Thursday, Nov 12 · 12:00 PM ET". Do not enter until scheduled.' ),
				array( 'key' => 'mc_join_url', 'label' => 'Webinar attendance link', 'type' => 'url', 'desc' => 'Shown on the registration thank-you page. Anyone who finds the page can see it – use a registration-gated link if possible.' ),
				array( 'key' => 'mc_replay_url', 'label' => 'Replay URL', 'type' => 'url' ),
				array( 'key' => 'mc_mode', 'label' => 'Masterclass page mode', 'type' => 'select', 'options' => array( 'register' => 'Registration open', 'replay' => 'Event over – show replay & fit-call CTA' ) ),
			),
		),
		'quiz'      => array(
			'label'  => __( 'Quiz', 'fvr' ),
			'fields' => array(
				array( 'key' => 'quiz_live', 'label' => 'Quiz is live for the public', 'type' => 'checkbox', 'desc' => 'OFF until the client approves the questions and routing. Logged-in editors can always preview it.' ),
				array( 'key' => 'quiz_capture', 'label' => 'Email capture on last step', 'type' => 'select', 'options' => array( 'optional' => 'Optional (with consent)', 'off' => 'Off – results only' ) ),
				array( 'key' => 'quiz_tie_break', 'label' => 'Tie-break result', 'type' => 'select', 'options' => array( 'A' => 'A – Foundations', 'B' => 'B – Routine', 'C' => 'C – Coaching' ), 'desc' => 'Used when two outcomes score equally (sample default – needs client approval).' ),
			),
		),
		'tracking'  => array(
			'label'  => __( 'Tracking', 'fvr' ),
			'fields' => array(
				array( 'key' => 'gtm_id', 'label' => 'Google Tag Manager ID', 'type' => 'text', 'desc' => 'GTM-XXXXXXX. Loaded ONLY after the visitor accepts the consent banner.' ),
				array( 'key' => 'ga4_id', 'label' => 'GA4 Measurement ID', 'type' => 'text', 'desc' => 'G-XXXXXXXXXX. Use this OR GTM, not both.' ),
				array( 'key' => 'meta_pixel_id', 'label' => 'Meta Pixel ID', 'type' => 'text' ),
			),
		),
	);
}

add_action(
	'admin_menu',
	static function () {
		add_menu_page( __( 'Reset Settings', 'fvr' ), __( 'Reset Settings', 'fvr' ), 'manage_options', 'fvr-settings', 'fvr_render_settings_page', 'dashicons-admin-generic', 23 );
	}
);

add_action(
	'admin_init',
	static function () {
		if ( false === get_option( 'fvr_settings' ) ) {
			add_option( 'fvr_settings', array(), '', false );
		}
		register_setting( 'fvr_settings_group', 'fvr_settings', array( 'type' => 'array', 'sanitize_callback' => 'fvr_sanitize_settings' ) );
	}
);

function fvr_sanitize_settings( $in ) {
	$old    = (array) get_option( 'fvr_settings', array() );
	$schema = fvr_settings_schema();
	$tab    = isset( $in['_tab'] ) ? sanitize_key( $in['_tab'] ) : '';
	if ( ! current_user_can( 'manage_options' ) || ! isset( $schema[ $tab ] ) ) {
		return $old;
	}
	$new = $old;
	foreach ( $schema[ $tab ]['fields'] as $f ) {
		$k   = $f['key'];
		$raw = $in[ $k ] ?? null;
		switch ( $f['type'] ) {
			case 'checkbox':
				$new[ $k ] = empty( $raw ) ? 0 : 1;
				break;
			case 'url':
				$new[ $k ] = esc_url_raw( trim( (string) $raw ), array( 'https', 'http' ) );
				break;
			case 'email':
				$new[ $k ] = sanitize_email( (string) $raw );
				break;
			case 'textarea':
				$new[ $k ] = sanitize_textarea_field( (string) $raw );
				break;
			case 'media':
				$new[ $k ] = absint( $raw );
				break;
			case 'id':
				$new[ $k ] = preg_replace( '/\D+/', '', (string) $raw );
				break;
			case 'select':
				$new[ $k ] = array_key_exists( (string) $raw, $f['options'] ) ? (string) $raw : '';
				break;
			case 'secret':
				$v = trim( (string) $raw );
				if ( 'CLEAR' === $v ) {
					$new[ $k ] = '';
				} elseif ( '' !== $v ) {
					$new[ $k ] = sanitize_text_field( $v );
				}
				break;
			default:
				$new[ $k ] = sanitize_text_field( (string) $raw );
		}
	}
	return $new;
}

/** Launch-readiness register: items still needing client approval or credentials. */
function fvr_pending_items(): array {
	$faq_drafts  = count( get_posts( array( 'post_type' => 'fvr_faq', 'post_status' => 'draft', 'posts_per_page' => 50, 'fields' => 'ids' ) ) );
	$legal_draft = 0;
	foreach ( array( 'privacy-policy', 'terms' ) as $slug ) {
		$p = get_page_by_path( $slug );
		if ( ! $p || 'publish' !== $p->post_status ) {
			++$legal_draft;
		}
	}
	$dummy = (array) get_option( 'fvr_dummy_items', array() );
	return array(
		array( 'Approved Reset price & terms entered (Pricing tab)', (bool) ( fvr_opt( 'price_text' ) && fvr_opt( 'price_show' ) ) ),
		array( 'Coaching check-in frequency / format / provider confirmed', (bool) fvr_opt( 'checkin_details' ) ),
		array( 'Founder portrait uploaded', (bool) fvr_opt( 'founder_photo_id' ) ),
		array( 'Founder credentials & experience statement verified', (bool) fvr_opt( 'founder_experience' ) ),
		array( 'Kit API key + form/tag IDs entered', (bool) ( fvr_kit_api_key() && fvr_opt( 'kit_form_checklist' ) ) ),
		array( 'Calendly URL confirmed', (bool) fvr_opt( 'calendly_url' ) ),
		array( 'Stripe link – Reset enrolment (after price approval)', (bool) fvr_opt( 'stripe_reset_url' ) ),
		array( 'Stripe link – Clarity coaching ($100)', (bool) fvr_opt( 'stripe_clarity_url' ) ),
		array( 'Fulfilment mechanism for Reset access defined (outside theme)', false ),
		array( 'Physician-review wording for Clarity page confirmed (outside theme)', false ),
		array( 'Checklist PDF URL (or email delivery confirmed)', (bool) fvr_opt( 'checklist_url' ) ),
		array( 'Masterclass date, attendance link and replay', (bool) ( fvr_opt( 'mc_datetime' ) && fvr_opt( 'mc_join_url' ) ) ),
		array( 'Quiz questions & routing approved, quiz set live', (bool) fvr_opt( 'quiz_live' ) ),
		array( sprintf( 'FAQ answers approved (%d draft FAQ entries remain)', $faq_drafts ), 0 === $faq_drafts ),
		array( 'Privacy Policy and Terms published (client-supplied legal text)', 0 === $legal_draft ),
		array( 'Tracking IDs (optional) added', (bool) fvr_tracking_enabled() ),
		array( sprintf( 'Sample/dummy content replaced with approved content (%d items flagged)', count( $dummy ) ), empty( $dummy ) ),
	);
}

function fvr_render_field( array $f, array $vals ): void {
	$k    = $f['key'];
	$name = 'fvr_settings[' . $k . ']';
	$val  = $vals[ $k ] ?? '';
	if ( '' === $val && isset( fvr_defaults()[ $k ] ) && ! array_key_exists( $k, $vals ) ) {
		$val = fvr_defaults()[ $k ];
	}
	switch ( $f['type'] ) {
		case 'checkbox':
			printf( '<label><input type="checkbox" name="%s" value="1" %s> %s</label>', esc_attr( $name ), checked( ! empty( $val ), true, false ), esc_html__( 'Enabled', 'fvr' ) );
			break;
		case 'textarea':
			printf( '<textarea class="large-text" rows="5" name="%s">%s</textarea>', esc_attr( $name ), esc_textarea( (string) $val ) );
			break;
		case 'select':
			echo '<select name="' . esc_attr( $name ) . '">';
			foreach ( $f['options'] as $ov => $ol ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $ov ), selected( (string) $val, (string) $ov, false ), esc_html( $ol ) );
			}
			echo '</select>';
			break;
		case 'media':
			$img = $val ? wp_get_attachment_image( (int) $val, 'thumbnail' ) : '';
			echo '<div class="fvr-media"><div class="fvr-media__preview">' . $img . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
			printf( '<input type="hidden" name="%s" value="%d">', esc_attr( $name ), (int) $val );
			echo '<button type="button" class="button fvr-media__pick">' . esc_html__( 'Choose image', 'fvr' ) . '</button> <button type="button" class="button-link fvr-media__clear">' . esc_html__( 'Remove', 'fvr' ) . '</button></div>';
			break;
		case 'secret':
			$set = ( 'kit_api_key' === $k && defined( 'FVR_KIT_API_KEY' ) ) ? __( 'Set in wp-config.php', 'fvr' ) : ( $val ? __( 'Saved – enter a new value to replace', 'fvr' ) : '' );
			printf( '<input type="password" class="regular-text" autocomplete="new-password" name="%s" value="" placeholder="%s">', esc_attr( $name ), esc_attr( $set ) );
			break;
		default:
			$type = ( 'url' === $f['type'] ) ? 'url' : ( 'email' === $f['type'] ? 'email' : 'text' );
			printf( '<input type="%s" class="regular-text" name="%s" value="%s">', esc_attr( $type ), esc_attr( $name ), esc_attr( (string) $val ) );
	}
	if ( ! empty( $f['desc'] ) ) {
		echo '<p class="description">' . esc_html( $f['desc'] ) . '</p>';
	}
}

function fvr_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$schema = fvr_settings_schema();
	$tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'links'; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! isset( $schema[ $tab ] ) ) {
		$tab = 'links';
	}
	$vals = (array) get_option( 'fvr_settings', array() );
	echo '<div class="wrap fvr-admin"><h1>' . esc_html__( 'FoundVital Reset – Settings', 'fvr' ) . '</h1>';
	if ( isset( $_GET['fvr_setup'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Setup ran: pages, FAQ drafts and sample quiz questions were created where missing.', 'fvr' ) . '</p></div>';
	}
	echo '<nav class="nav-tab-wrapper">';
	foreach ( $schema as $slug => $t ) {
		printf( '<a class="nav-tab %s" href="%s">%s</a>', $slug === $tab ? 'nav-tab-active' : '', esc_url( add_query_arg( array( 'page' => 'fvr-settings', 'tab' => $slug ), admin_url( 'admin.php' ) ) ), esc_html( $t['label'] ) );
	}
	echo '</nav><div class="fvr-admin__grid"><form method="post" action="options.php">';
	settings_fields( 'fvr_settings_group' );
	echo '<input type="hidden" name="fvr_settings[_tab]" value="' . esc_attr( $tab ) . '"><table class="form-table" role="presentation">';
	foreach ( $schema[ $tab ]['fields'] as $f ) {
		echo '<tr><th scope="row"><label>' . esc_html( $f['label'] ) . '</label></th><td>';
		fvr_render_field( $f, $vals );
		echo '</td></tr>';
	}
	echo '</table>';
	submit_button();
	echo '</form><aside class="fvr-admin__side"><h2>' . esc_html__( 'Launch readiness', 'fvr' ) . '</h2><ul class="fvr-checklist">';
	foreach ( fvr_pending_items() as $item ) {
		printf( '<li class="%s"><span aria-hidden="true">%s</span> %s</li>', $item[1] ? 'is-done' : 'is-open', $item[1] ? '✔' : '○', esc_html( $item[0] ) );
	}
	echo '</ul>';
	$dummy_list = (array) get_option( 'fvr_dummy_items', array() );
	if ( $dummy_list ) {
		echo '<div class="notice notice-warning inline"><p><strong>' . esc_html__( 'Sample content is live in:', 'fvr' ) . '</strong><br>' . esc_html( implode( ' · ', $dummy_list ) ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'fvr_clear_dummy' );
		echo '<input type="hidden" name="action" value="fvr_clear_dummy">';
		submit_button( __( 'I have replaced it all – clear this warning', 'fvr' ), 'link', 'submit', false );
		echo '</form></div>';
	}
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	wp_nonce_field( 'fvr_run_setup' );
	echo '<input type="hidden" name="action" value="fvr_run_setup">';
	submit_button( __( 'Run page & content setup again', 'fvr' ), 'secondary', 'submit', false );
	echo '<p class="description">' . esc_html__( 'Creates any missing pages, FAQ drafts and sample quiz questions. Never overwrites existing content.', 'fvr' ) . '</p></form></aside></div></div>';
}

add_action(
	'admin_post_fvr_clear_dummy',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'fvr' ), 403 );
		}
		check_admin_referer( 'fvr_clear_dummy' );
		delete_option( 'fvr_dummy_items' );
		wp_safe_redirect( add_query_arg( array( 'page' => 'fvr-settings' ), admin_url( 'admin.php' ) ) );
		exit;
	}
);
