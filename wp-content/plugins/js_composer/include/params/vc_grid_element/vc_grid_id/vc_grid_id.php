<?php
/**
 * Param type 'vc_grid_id'.
 *
 * Specific param type for vc_grid_id that we use for our grid builder.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Render form field for vc_grid_id.
 *
 * @param array $settings
 * @param string $value
 *
 * @return string
 * @since 4.4.3
 */
function vc_vc_grid_id_form_field( $settings, $value ) {
	$output = '<div class="vc_param-vc-grid-id">';
	$output .= WPB_Form_Field_Hidden::get([
		'name' => $settings['param_name'],
		'classes' => 'wpb_vc_param_value ' . $settings['param_name'] . ' ' . $settings['type'] . '_field',
		'value' => $value,
	]);
	$output .= '</div>';

	return $output;
}
