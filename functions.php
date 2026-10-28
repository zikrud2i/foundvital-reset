<?php
/**
 * FoundVital Reset theme bootstrap.
 *
 * @package foundvital-reset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FVR_VERSION', '1.0.0' );
define( 'FVR_DIR', get_template_directory() );
define( 'FVR_URI', get_template_directory_uri() );

$fvr_includes = array(
	'helpers',
	'theme-setup',
	'assets',
	'settings',
	'custom-fields',
	'cpt',
	'integrations',
	'forms',
	'quiz',
	'security',
	'seo',
	'setup',
);

foreach ( $fvr_includes as $fvr_file ) {
	require_once FVR_DIR . '/inc/' . $fvr_file . '.php';
}
unset( $fvr_includes, $fvr_file );
