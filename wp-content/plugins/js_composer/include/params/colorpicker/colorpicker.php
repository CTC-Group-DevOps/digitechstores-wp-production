<?php
/**
 * Param type 'colorpicker'.
 *
 * Used to create colorpicker field.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Param 'colorpicker' field
 *
 * @param array $settings
 * @param string $value
 * @param string $tag
 * @param string $param_id
 *
 * @return string
 * @since 4.4
 */
function vc_colorpicker_form_field( $settings, $value, $tag, $param_id ) {
	$default_colorpicker_color =
		$settings['settings']['default_colorpicker_color'] ??
		$settings['default_colorpicker_color'] ??
		'';

	return WPB_Form_Field_Colorpicker::get(
		[
			'id'                => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
			'classes'           => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] . '_field' ),
			'name'              => $settings['param_name'],
			'value'             => $value,
			'data_attributes'    => [
				'default-colorpicker-color' => $default_colorpicker_color,
			],
		]
	);
}
