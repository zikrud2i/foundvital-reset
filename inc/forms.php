<?php
/**
 * Lead forms (checklist, masterclass): rendering + secure admin-post handler.
 * Redirects to the thank-you page only after Kit confirms the subscription.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fvr_error_message( string $code ): string {
	$map = array(
		'nonce'   => 'Your session expired. Please reload the page and try again.',
		'spam'    => 'We couldn\'t verify that submission. Please wait a few seconds and try again.',
		'rate'    => 'Too many attempts. Please try again in a little while.',
		'captcha' => 'Please complete the spam check and try again.',
		'name'    => 'Please enter your first name.',
		'email'   => 'Please enter a valid email address.',
		'state'   => 'Please choose your state of residence.',
		'consent' => 'Please tick the consent box so we can email you.',
		'config'  => 'Sign-up isn\'t available right now. Please try again later.',
		'kit'     => 'We couldn\'t complete your sign-up just now. Please try again in a moment.',
		'invalid' => 'Something went wrong. Please try again.',
		'quizmiss'=> 'Please answer every question to see your result.',
	);
	return $map[ $code ] ?? $map['invalid'];
}

/** Where to send the visitor back to (never an arbitrary URL). */
function fvr_return_url(): string {
	$id = isset( $_POST['fvr_return'] ) ? absint( $_POST['fvr_return'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
	if ( $id && 'publish' === get_post_status( $id ) ) {
		return get_permalink( $id );
	}
	return home_url( '/' );
}

/** True when the form was sent in the background by main.js (expects JSON, not a redirect). */
function fvr_is_ajax_submit(): bool {
	return ! empty( $_POST['fvr_ajax'] ); // phpcs:ignore WordPress.Security.NonceVerification -- nonce is checked by the handler.
}

function fvr_redirect_error( string $code, string $anchor = 'signup' ): void {
	if ( fvr_is_ajax_submit() ) {
		wp_send_json_error( array( 'message' => fvr_error_message( $code ) ) );
	}
	wp_safe_redirect( add_query_arg( 'fvr_err', $code, fvr_return_url() ) . '#' . $anchor );
	exit;
}

/** Visible error banner (reads ?fvr_err=). */
function fvr_render_form_error(): void {
	if ( empty( $_GET['fvr_err'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$code = sanitize_key( wp_unslash( $_GET['fvr_err'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	echo '<div class="alert alert--error" role="alert">' . esc_html( fvr_error_message( $code ) );
	if ( 'config' === $code && current_user_can( 'manage_options' ) ) {
		echo ' <em>(' . esc_html__( 'Admin: add the Kit API key in Reset Settings > Kit & forms.', 'fvr' ) . ')</em>';
	}
	echo '</div>';
}

/** Link to the privacy page when published. */
function fvr_privacy_link(): string {
	$p = get_page_by_path( 'privacy-policy' );
	if ( $p && 'publish' === $p->post_status ) {
		return ' <a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html__( 'Privacy Policy', 'fvr' ) . '</a>.';
	}
	return '';
}

/** Local file behind the fixed PDF address: the PDF chosen in Reset Settings, else the copy bundled with the theme. */
function fvr_checklist_pdf_file(): string {
	$src = (string) fvr_opt( 'checklist_url' );
	$id  = $src ? attachment_url_to_postid( $src ) : 0;
	$file = $id ? (string) get_attached_file( $id ) : '';
	if ( $file && is_readable( $file ) ) {
		return $file;
	}
	$bundled = FVR_DIR . '/assets/downloads/seven-questions-checklist.pdf';
	return is_readable( $bundled ) ? $bundled : '';
}

/** Fixed public address of the checklist PDF (used in Kit emails, so it must never change). '' when there is no PDF. */
function fvr_checklist_pdf_url(): string {
	return ( fvr_checklist_pdf_file() || fvr_opt( 'checklist_url' ) ) ? home_url( '/downloads/seven-questions-checklist.pdf' ) : '';
}

/**
 * Serve /downloads/seven-questions-checklist.pdf (see fvr_checklist_pdf_file()) with a noindex
 * header so it stays out of search results. Swapping the PDF never changes this address.
 */
add_action(
	'init',
	static function () {
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		if ( trim( substr( $uri, strlen( $base ) - 1 ), '/' ) !== 'downloads/seven-questions-checklist.pdf' ) {
			return;
		}
		$src  = (string) fvr_opt( 'checklist_url' );
		$file = fvr_checklist_pdf_file();
		if ( ! $file ) {
			if ( $src ) {
				header( 'X-Robots-Tag: noindex, nofollow' );
				wp_redirect( $src, 302 ); // phpcs:ignore WordPress.Security.SafeRedirect -- admin-set file URL.
				exit;
			}
			status_header( 404 );
			nocache_headers();
			exit;
		}
		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: inline; filename="seven-questions-checklist.pdf"' );
		header( 'Content-Length: ' . filesize( $file ) );
		header( 'X-Robots-Tag: noindex, nofollow' );
		header( 'Cache-Control: public, max-age=3600' );
		readfile( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	},
	0
);

/** The 50 US states, for the "State of residence" field. */
function fvr_us_states(): array {
	return array( 'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming' );
}

/** Posted state, or '' when missing or not one of the 50 states. */
function fvr_posted_state(): string {
	$state = isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	return in_array( $state, fvr_us_states(), true ) ? $state : '';
}

/** "State of residence" dropdown. */
function fvr_state_field( string $suffix ): void {
	?>
	<div class="field">
		<label for="fvr-st-<?php echo esc_attr( $suffix ); ?>"><?php esc_html_e( 'State of residence', 'fvr' ); ?> <span class="req" aria-hidden="true">*</span></label>
		<select id="fvr-st-<?php echo esc_attr( $suffix ); ?>" name="state" autocomplete="address-level1" required aria-describedby="fvr-sterr-<?php echo esc_attr( $suffix ); ?>">
			<option value=""><?php esc_html_e( 'Select your state', 'fvr' ); ?></option>
			<?php foreach ( fvr_us_states() as $st ) : ?>
				<option value="<?php echo esc_attr( $st ); ?>"><?php echo esc_html( $st ); ?></option>
			<?php endforeach; ?>
		</select>
		<p class="field__error" id="fvr-sterr-<?php echo esc_attr( $suffix ); ?>" role="alert" hidden></p>
	</div>
	<?php
}

/** Explains the asterisk; shown at the top of every sign-up form. */
function fvr_required_note(): void {
	echo '<p class="form__required">' . esc_html__( 'Fields marked with an asterisk (*) are required.', 'fvr' ) . '</p>';
}

/** Line shown under every sign-up form. Links to the policy once it is published. */
function fvr_signup_privacy_note(): void {
	$p     = get_page_by_path( 'privacy-policy' );
	$label = esc_html__( 'Privacy Policy', 'fvr' );
	if ( $p && 'publish' === $p->post_status ) {
		$label = '<a href="' . esc_url( get_permalink( $p ) ) . '">' . $label . '</a>';
	}
	echo '<p class="fineprint form__privacy">' . esc_html__( 'By signing up you agree to our', 'fvr' ) . ' ' . $label . '.</p>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
}

/**
 * Render a lead form. Uses Forminator instead when a form ID is configured.
 *
 * @param string $type   checklist|masterclass.
 * @param string $button Button label.
 */
function fvr_lead_form( string $type, string $button, string $consent = '' ): void {
	$forminator_id = (int) fvr_opt( 'forminator_' . $type . '_id' );
	if ( $forminator_id && shortcode_exists( 'forminator_form' ) ) {
		echo do_shortcode( '[forminator_form id="' . $forminator_id . '"]' );
		fvr_signup_privacy_note();
		return;
	}
	if ( '' === $consent ) {
		$consent = 'Yes, email me this resource and occasional education and updates from FoundVital. I can unsubscribe at any time.';
	}
	echo '<form class="form" id="signup" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" novalidate data-form-type="' . esc_attr( $type ) . '">';
	fvr_render_form_error();
	echo '<input type="hidden" name="action" value="fvr_subscribe"><input type="hidden" name="fvr_type" value="' . esc_attr( $type ) . '"><input type="hidden" name="fvr_return" value="' . esc_attr( (string) get_the_ID() ) . '">';
	wp_nonce_field( 'fvr_subscribe_' . $type, '_fvr_nonce' );
	fvr_spam_fields();
	fvr_required_note();
	?>
	<div class="field">
		<label for="fvr-fn-<?php echo esc_attr( $type ); ?>"><?php esc_html_e( 'First name', 'fvr' ); ?> <span class="req" aria-hidden="true">*</span></label>
		<input id="fvr-fn-<?php echo esc_attr( $type ); ?>" type="text" name="first_name" autocomplete="given-name" maxlength="60" required aria-describedby="fvr-fnerr-<?php echo esc_attr( $type ); ?>">
		<p class="field__error" id="fvr-fnerr-<?php echo esc_attr( $type ); ?>" role="alert" hidden></p>
	</div>
	<div class="field">
		<label for="fvr-em-<?php echo esc_attr( $type ); ?>"><?php esc_html_e( 'Email address', 'fvr' ); ?> <span class="req" aria-hidden="true">*</span></label>
		<input id="fvr-em-<?php echo esc_attr( $type ); ?>" type="email" name="email" autocomplete="email" required aria-describedby="fvr-err-<?php echo esc_attr( $type ); ?>">
		<p class="field__error" id="fvr-err-<?php echo esc_attr( $type ); ?>" role="alert" hidden></p>
	</div>
	<?php fvr_state_field( $type ); ?>
	<div class="field field--check">
		<input id="fvr-co-<?php echo esc_attr( $type ); ?>" type="checkbox" name="consent" value="1" required>
		<label for="fvr-co-<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $consent ); ?> <span class="req" aria-hidden="true">*</span></label>
	</div>
	<button class="btn btn--primary btn--block" type="submit"><span><?php echo esc_html( $button ); ?></span><?php echo fvr_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	<?php
	fvr_signup_privacy_note();
	echo '</form>';
	if ( 'checklist' === $type ) {
		fvr_checklist_success( true );
	}
}

/** "Your checklist is on its way" message with the download button (in-page after sign-up, and on the thank-you page). */
function fvr_checklist_success( bool $hidden = false ): void {
	$pdf = fvr_checklist_pdf_url();
	echo '<div class="form-success" id="signup-success" tabindex="-1"' . ( $hidden ? ' hidden' : '' ) . '>';
	echo '<p class="form-success__title">' . esc_html__( 'Thank you! Your checklist is on its way to your inbox.', 'fvr' ) . '</p>';
	if ( $pdf ) {
		echo '<p>' . esc_html__( 'You can also download it now:', 'fvr' ) . '</p>';
		fvr_btn( __( 'Download the Checklist (PDF)', 'fvr' ), $pdf, 'primary', 'checklist_download_click' );
	}
	echo '</div>';
}

add_action( 'admin_post_nopriv_fvr_subscribe', 'fvr_handle_subscribe' );
add_action( 'admin_post_fvr_subscribe', 'fvr_handle_subscribe' );

function fvr_handle_subscribe(): void {
	$type = isset( $_POST['fvr_type'] ) ? sanitize_key( wp_unslash( $_POST['fvr_type'] ) ) : '';
	if ( ! in_array( $type, array( 'checklist', 'masterclass' ), true ) ) {
		fvr_redirect_error( 'invalid' );
	}
	$nonce = isset( $_POST['_fvr_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_fvr_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'fvr_subscribe_' . $type ) ) {
		fvr_redirect_error( 'nonce' );
	}
	$spam = fvr_spam_check( 'subscribe_' . $type );
	if ( $spam ) {
		fvr_redirect_error( $spam );
	}
	$first = isset( $_POST['first_name'] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST['first_name'] ) ), 0, 60 ) : '';
	if ( '' === trim( $first ) ) {
		fvr_redirect_error( 'name' );
	}
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		fvr_redirect_error( 'email' );
	}
	$state = fvr_posted_state();
	if ( '' === $state ) {
		fvr_redirect_error( 'state' );
	}
	if ( empty( $_POST['consent'] ) ) {
		fvr_redirect_error( 'consent' );
	}

	$result = fvr_kit_subscribe(
		$email,
		$first,
		(int) fvr_opt( 'kit_form_' . $type ),
		array( (int) fvr_opt( 'kit_tag_' . $type ) ),
		array( 'state' => $state )
	);
	if ( is_wp_error( $result ) ) {
		fvr_redirect_error( 'kit_unconfigured' === $result->get_error_code() ? 'config' : 'kit' );
	}

	if ( fvr_is_ajax_submit() ) {
		wp_send_json_success();
	}
	fvr_set_success_cookie( $type );
	wp_safe_redirect( fvr_thanks_url( $type ) );
	exit;
}
