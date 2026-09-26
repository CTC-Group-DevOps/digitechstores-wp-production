<?php
/**
 * Param type 'el_id'.
 *
 * Used to create input text field specifically for element ID.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Get output for el_id form field.
 *
 * @param array $settings
 * @param string $value
 * @param string $tag
 * @param string $param_id
 *
 * @return string
 * @since 4.5
 */
function vc_el_id_form_field( $settings, $value, $tag, $param_id ) {
	$value_output = vc_get_template( 'params/el_id/template.php', [
		'settings' => $settings,
		'value' => $value,
		'param_id' => $param_id,
	] );

	return apply_filters( 'vc_el_id_render_filter', $value_output );
}
