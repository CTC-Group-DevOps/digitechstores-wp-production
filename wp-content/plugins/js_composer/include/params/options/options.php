<?php
/**
 * Param type 'options'.
 *
 * Used to create options form field.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Renders the options param form field.
 *
 * @param array $settings
 * @param string $value
 *
 * @return string
 * @since 4.2
 */
function vc_options_form_field( $settings, $value ) {
	$options = isset( $settings['options'] ) ? $settings['options'] : [];
	return sprintf(
		'<div class="vc_options">%s<a href="#" class="button vc_options-edit %s_button">%s</a></div><div class="vc_options-fields" data-settings="%s"><a href="#" class="button vc_close-button">%s</a></div>',
		WPB_Form_Field_Hidden::get([
			'name' => $settings['param_name'],
			'classes' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] . '_field' ),
			'value' => $value,
			'is_value_escape' => false,
		]),
		esc_attr( $settings['param_name'] ),
		esc_html__( 'Manage options', 'js_composer' ),
		htmlspecialchars( wp_json_encode( $options ) ),
		esc_html__( 'Close', 'js_composer' )
	);
}

/**
 * Include options templates.
 *
 * @since 4.2
 */
function vc_options_include_templates() {
	require_once vc_path_dir( 'TEMPLATES_DIR', 'params/options/templates.html' );
}

add_action( 'admin_footer', 'vc_options_include_templates' );
