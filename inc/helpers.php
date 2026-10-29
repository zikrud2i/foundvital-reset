<?php
/**
 * Shared helpers: settings access, page fields, buttons, icons, small utilities.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defaults for site-wide settings. Only values taken from the client documents
 * are pre-filled. Anything unconfirmed (price, check-in frequency, dates) is blank.
 */
function fvr_defaults(): array {
	return array(
		'calendly_url'        => 'https://calendly.com/care-foundvital/fitcall',
		'outfitmd_url'        => 'https://shop.foundvital.com/',
		'lab_page_url'        => 'https://foundvital.com/lab-consultation',
		'founder_name'        => 'Tara',
		'founder_credentials' => 'PA-C, DipACLM',
		'cta_primary_label'   => 'Book a Reset Fit Conversation',
		'cta_secondary_label' => 'Take the Reset Quiz',
		'quiz_tie_break'      => 'B',
		'quiz_capture'        => 'required',
		'clarity_test_price'  => '$99',
		'clarity_coach_price' => '$100',
		'clarity_total_price' => '$199',
		'footer_disclaimer'   => 'FoundVital Reset is non-medical health coaching and education. It is not a diagnostic, treatment, or medication-access service, and it does not replace care from your licensed healthcare provider.',
	);
}

/**
 * Read a theme setting (option "fvr_settings").
 *
 * @param string $key     Setting key.
 * @param string $default Fallback when the setting is blank.
 * @return mixed
 */
function fvr_opt( string $key, $default = '' ) {
	$all = wp_parse_args( (array) get_option( 'fvr_settings', array() ), fvr_defaults() );
	$val = $all[ $key ] ?? '';
	if ( '' === $val || null === $val ) {
		return $default;
	}
	return $val;
}

/**
 * Read a per-page content field. Works with Secure Custom Fields (which stores
 * values as plain post meta) and also without it, so templates never fatal.
 *
 * @param string $name    Field name.
 * @param mixed  $default Fallback when empty.
 * @param int    $post_id Post ID (defaults to current).
 * @return mixed
 */
function fvr_field( string $name, $default = '', int $post_id = 0 ) {
	$post_id = $post_id ? $post_id : (int) get_the_ID();
	if ( ! $post_id ) {
		return $default;
	}
	$val = get_post_meta( $post_id, $name, true );
	if ( '' === $val || null === $val || false === $val ) {
		return $default;
	}
	return $val;
}

/** Split a textarea into non-empty trimmed lines. */
function fvr_lines( $text ): array {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/** Permalink for a page path, falling back to a pretty URL guess. */
function fvr_page_url( string $path ): string {
	$page = get_page_by_path( $path );
	if ( $page && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}
	return home_url( '/' . trim( $path, '/' ) . '/' );
}

/** Is this URL on another host? */
function fvr_is_external( string $url ): bool {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( ! $host ) {
		return false;
	}
	return strtolower( $host ) !== strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
}

/**
 * Print a button/link.
 *
 * @param string $label   Visible text.
 * @param string $url     Destination.
 * @param string $variant primary|secondary|ghost|gold|light.
 * @param string $track   Optional analytics event name (data-track).
 * @param string $class   Extra classes.
 */
function fvr_btn( string $label, string $url, string $variant = 'primary', string $track = '', string $class = '' ): void {
	if ( '' === $url ) {
		return;
	}
	$ext   = fvr_is_external( $url );
	$attrs = $ext ? ' target="_blank" rel="noopener noreferrer"' : '';
	if ( $track ) {
		$attrs .= ' data-track="' . esc_attr( $track ) . '"';
	}
	printf(
		'<a class="btn btn--%1$s %2$s" href="%3$s"%4$s><span>%5$s</span>%6$s%7$s</a>',
		esc_attr( $variant ),
		esc_attr( $class ),
		esc_url( $url ),
		$attrs, // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above.
		esc_html( $label ),
		fvr_icon( $ext ? 'external' : 'arrow' ), // phpcs:ignore WordPress.Security.EscapeOutput
		$ext ? '<span class="screen-reader-text"> ' . esc_html__( '(opens in a new tab)', 'fvr' ) . '</span>' : '' // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

/** Primary CTA: Fit Conversation. */
function fvr_fit_btn( string $variant = 'primary', string $label = '', string $track = 'cta_fit_call' ): void {
	fvr_btn( $label ? $label : (string) fvr_opt( 'cta_primary_label' ), fvr_page_url( 'fit-call' ), $variant, $track );
}

/** Secondary CTA: Quiz. */
function fvr_quiz_btn( string $variant = 'secondary', string $label = '', string $track = 'cta_quiz' ): void {
	fvr_btn( $label ? $label : (string) fvr_opt( 'cta_secondary_label' ), fvr_page_url( 'quiz' ), $variant, $track );
}

/** Editor-only reminder for content that still needs client approval. */
function fvr_pending( string $message ): void {
	if ( current_user_can( 'edit_pages' ) ) {
		echo '<p class="pending-note"><strong>' . esc_html__( 'Editor-only note:', 'fvr' ) . '</strong> ' . esc_html( $message ) . '</p>';
	}
}

/** Inline SVG icons (decorative). */
function fvr_icon( string $name, string $class = 'icon' ): string {
	$paths = array(
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'external' => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
		'check'    => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		'x'        => '<path d="M6 6l12 12M18 6L6 18"/>',
		'compass'  => '<circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2 5-5 2 2-5z"/>',
		'leaf'     => '<path d="M5 19c0-8 5-13 14-14 0 9-5 14-13 14"/><path d="M5 19c2-5 5-8 9-10"/>',
		'chat'     => '<path d="M4 5h16v11H9l-5 4z"/>',
		'toolkit'  => '<rect x="3" y="8" width="18" height="12" rx="2"/><path d="M9 8V6a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2M3 13h18"/>',
		'calendar' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'shield'   => '<path d="M12 3l8 3v6c0 4.5-3.2 7.8-8 9-4.8-1.2-8-4.5-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
		'list'     => '<path d="M9 6h11M9 12h11M9 18h11M4 6h.01M4 12h.01M4 18h.01"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'play'     => '<circle cx="12" cy="12" r="9"/><path d="M10 8.5l6 3.5-6 3.5z"/>',
		'flask'    => '<path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3"/>',
		'plus'     => '<path d="M12 5v14M5 12h14"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.6" fill="currentColor"/>',
		'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
		'threads'  => '<path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/><circle cx="12" cy="12" r="4"/>',
		'youtube'  => '<path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/** Logo markup (setting → bundled fallback). */
function fvr_logo(): string {
	$id = (int) fvr_opt( 'logo_id', 0 );
	if ( $id ) {
		$img = wp_get_attachment_image( $id, 'medium', false, array( 'class' => 'site-logo__img', 'alt' => get_bloginfo( 'name' ) . ', FoundVital', 'decoding' => 'async' ) );
		if ( $img ) {
			return $img;
		}
	}
	return sprintf(
		'<img class="site-logo__img" src="%s" width="150" height="51" alt="FoundVital" decoding="async">',
		esc_url( FVR_URI . '/assets/images/foundvital-logo.png' )
	);
}

/** Output an attachment image from a setting or field, or nothing. */
function fvr_attachment( int $id, string $size = 'large', array $attr = array() ): string {
	if ( ! $id ) {
		return '';
	}
	return (string) wp_get_attachment_image( $id, $size, false, $attr );
}

/** Founder display line, e.g. "Tara, PA-C, DipACLM". */
function fvr_founder_line(): string {
	$name  = (string) fvr_opt( 'founder_name' );
	$creds = (string) fvr_opt( 'founder_credentials' );
	return trim( $name . ( $creds ? ', ' . $creds : '' ) );
}

/** URL of one of the thank-you pages. */
function fvr_thanks_url( string $type ): string {
	return fvr_page_url( 'thank-you/' . $type );
}

/** Render FAQ items (published only). Returns number rendered. */
function fvr_render_faqs(): int {
	$faqs = get_posts(
		array(
			'post_type'      => 'fvr_faq',
			'post_status'    => 'publish',
			'posts_per_page' => 30,
			'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
	$shown = 0;
	foreach ( $faqs as $faq ) {
		// Never show unfinished placeholders, even if someone published the entry by mistake.
		if ( false !== stripos( $faq->post_content, '[CONFIRM' ) ) {
			continue;
		}
		++$shown;
		echo '<details class="faq__item"><summary>' . esc_html( get_the_title( $faq ) ) . '<span class="faq__icon">' . fvr_icon( 'plus' ) . '</span></summary><div class="faq__answer prose">' . wp_kses_post( wpautop( $faq->post_content ) ) . '</div></details>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	return $shown;
}

/**
 * Founder portrait: the Media Library image chosen in Reset Settings, otherwise the bundled photo of Tara.
 *
 * @param string $size  Image size for the Media Library version.
 * @param array  $attr  Attributes (class, loading, sizes, fetchpriority).
 */
function fvr_founder_photo( string $size = 'large', array $attr = array() ): string {
	$img = fvr_attachment( (int) fvr_opt( 'founder_photo_id', 0 ), $size, $attr );
	if ( $img ) {
		return $img;
	}
	$class = isset( $attr['class'] ) ? $attr['class'] : '';
	$load  = isset( $attr['loading'] ) ? $attr['loading'] : 'lazy';
	$prio  = isset( $attr['fetchpriority'] ) ? ' fetchpriority="' . esc_attr( $attr['fetchpriority'] ) . '"' : '';
	return sprintf(
		'<img class="%1$s" src="%2$s" width="450" height="495" alt="%3$s" loading="%4$s"%5$s decoding="async">',
		esc_attr( $class ),
		esc_url( FVR_URI . '/assets/images/tara.webp' ),
		esc_attr( sprintf( 'Portrait of %s', fvr_opt( 'founder_name' ) ) ),
		esc_attr( $load ),
		$prio // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
	);
}

/**
 * Hero headline with an italic, colored emphasis. Wrap words in *asterisks* in the SCF field to choose
 * the emphasis; otherwise everything after the first sentence is emphasised.
 */
function fvr_hero_headline( string $text ): string {
	$safe = esc_html( $text );
	if ( preg_match( '/\*(.+?)\*/', $safe ) ) {
		return preg_replace( '/\*(.+?)\*/', '<em class="hl">$1</em>', $safe );
	}
	if ( preg_match( '/^(.+?[.!?])\s+(.+)$/su', $safe, $m ) ) {
		return $m[1] . ' <em class="hl">' . $m[2] . '</em>';
	}
	return $safe;
}
