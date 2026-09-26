<?php
/**
 * Param type 'param_preset'.
 *
 * Container param for preset other fields.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Params preset shortcode attribute type generator.
 *
 * Allows to set list of attributes which will be
 *
 * @param array $settings
 * @param string $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_params_preset_form_field( $settings, $value ) {
	$output = '';
	$output .= '<select name="' . esc_attr( $settings['param_name'] ) . '"' . wpbakery()->editForm()->get_value_control_attr_class( $settings['param_name'], $settings['type'], '', 'vc_params-preset-select' ) . '>';
	$option_list = isset( $settings['options'] ) ? $settings['options'] : [];
	foreach ( $option_list as $option ) {
		$selected = '';
		if ( isset( $option['value'] ) ) {
			$option_value_string = (string) $option['value'];
			$value_string = (string) $value;
			if ( '' !== $value && $option_value_string === $value_string ) {
				$selected = 'selected';
			}
			$output .= '<option class="vc_params-preset-' . esc_attr( $option['value'] ) . '" value="' . esc_attr( $option['value'] ) . '" ' . $selected . ' data-params="' . esc_attr( wp_json_encode( $option['params'] ) ) . '">' . esc_html( isset( $option['label'] ) ? $option['label'] : $option['value'] ) . '</option>';
		}
	}
	$output .= '</select>';

	return $output;
}
