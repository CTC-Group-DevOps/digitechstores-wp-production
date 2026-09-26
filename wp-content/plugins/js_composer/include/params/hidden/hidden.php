<?php
/**
 * Param type 'hidden'.
 *
 * Used to create hidden field.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Hidden field param.
 *
 * @param array $settings
 * @param mixed $value
 *
 * @since 4.5
 * @return string - html string.
 */
function vc_hidden_form_field( $settings, $value ) {
	$value = is_string( $value ) ? $value : '';
	$value = htmlspecialchars( $value );

	return WPB_Form_Field_Hidden::get([
		'name' => $settings['param_name'],
		'value' => $value,
		'classes' => wpbakery()->editForm()->get_value_control_classes(
			$settings['param_name'],
			$settings['type'],
			'',
			'vc_hidden-field'
		),
	]);
}

/**
 * Remove content before hidden field type input.
 *
 * @since 4.5
 *
 * @return string
 */
function vc_edit_form_fields_render_field_hidden_before() {
	return '<div class="vc_column vc_edit-form-hidden-field-wrapper">';
}

/**
 * Remove content after hidden field type input.
 *
 * @since 4.5
 *
 * @return string
 */
function vc_edit_form_fields_render_field_hidden_after() {
	return '</div>';
}
