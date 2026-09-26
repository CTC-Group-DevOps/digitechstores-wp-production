<?php
/**
 * Param type "gutenberg".
 *
 * Used gutenberg editor as param type.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Gutenberg field param.
 *
 * @param array $settings
 * @param mixed $value
 *
 * @return string - html string.
 */
function vc_gutenberg_form_field( $settings, $value ) {
	$value = $value ?? '';
	$value = htmlspecialchars( $value );

	return vc_get_template( 'params/gutenberg/template.php', [
		'settings' => $settings,
		'value'    => $value,
	] );
}
