<?php
/**
 * Param type 'href'
 *
 * Use it to create url link button that lets the user insert additional url attributes.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Get href form field html.
 *
 * Delegates to the unified link param field.
 * href stores plain URLs - vc_build_link() handles this gracefully.
 *
 * @param array  $settings Parameter settings.
 * @param string $value    Current value (plain URL).
 * @param string $tag
 * @param string $param_id
 *
 * @return string
 * @since 4.4
 */
function vc_href_form_field( $settings, $value, $tag = '', $param_id = '' ) {
	if ( ! is_string( $value ) || strlen( $value ) === 0 || 'http://' === $value ) {
		$value = '';
	}

	// Convert plain URL to pipe-delimited format for the unified link template.
	if ( $value && false === strpos( $value, 'url:' ) ) {
		$value = 'url:' . rawurlencode( $value );
	}

	return vc_link_form_field( $settings, $value, $tag, $param_id );
}
