<?php
/**
 * Param type 'textarea_ace'.
 *
 * Used to create text area with Ace Editor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Get param form field html output.
 *
 * @param array $settings
 * @param mixed $value
 * @param string $tag
 * @param string $param_id
 *
 * @return string
 * @since 8.1
 */
function vc_textarea_ace_form_field( $settings, $value, $tag = '', $param_id = '' ) {
	$value = $value ?? '';

	$output = WPB_Form_Field_Textarea_Ace::get([
		'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
		'classes' => 'textarea_ace_container',
		'decoded_value' => rawurldecode( base64_decode( $value ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		'style' => 'width:100%;height:300px',
	]);
	$output .= WPB_Form_Field_Hidden::get([
		'name' => 'content',
		'value' => $value,
		'classes' => wpbakery()->editForm()->get_value_control_classes(
			$settings['param_name'],
			$settings['type'],
			'',
			'content'
		),
	]);

	return $output;
}
