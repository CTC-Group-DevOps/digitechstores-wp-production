<?php
/**
 * Param type 'vc_link'.
 *
 * Use it to create Link selection field.
 *
 * @note in shortcodes html output, use $href = vc_build_link( $href );
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Get link form field html.
 *
 * Delegates to the unified link param field.
 *
 * @param array  $settings Parameter settings.
 * @param string $value    Current value.
 * @param string $tag
 * @param string $param_id
 *
 * @return string
 * @since 4.2
 */
function vc_vc_link_form_field( $settings, $value, $tag = '', $param_id = '' ) {
	return vc_link_form_field( $settings, $value, $tag, $param_id );
}

/**
 * Get link form field attributes.
 *
 * @param array|string $value
 *
 * @return array
 * @since 4.2
 */
function vc_build_link( $value ) {
	return vc_parse_multi_attribute( $value, [
		'url' => '',
		'title' => '',
		'target' => '',
		'rel' => '',
	] );
}
