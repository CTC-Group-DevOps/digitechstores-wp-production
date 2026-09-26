<?php
/**
 * Param type 'tab_id'
 *
 * Used to create specific tab_id field for tabs.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Renders the form field for the tab_id param.
 *
 * @param array $settings
 * @param string $value
 *
 * @return string
 * @since 4.2
 */
function vc_tab_id_form_field( $settings, $value ) {
	$output = sprintf(
		'<div class="my_param_block">%s<label>%s</label></div>',
		WPB_Form_Field_Hidden::get([
			'name' => $settings['param_name'],
			'classes' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] . '_field' ),
			'value' => $value,
			'is_value_escape' => false,
		]),
		$value
	);

	return $output;
}
