<?php
/**
 * Param type 'textarea_html'.
 *
 * Used to create text area with default WordPress WYSIWYG Editor.
 *
 * @note: only one html textarea is permitted per shortcode and should have “content” as a param_name.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $vc_html_editor_already_is_use;
$vc_html_editor_already_is_use = false;
/**
 * Get param form field html output.
 *
 * @param array $settings
 * @param string $value
 * @param string $tag
 * @param string $param_id
 * @return string
 * @since 4.2
 */
function vc_textarea_html_form_field( $settings, $value, $tag, $param_id ) {
	global $vc_html_editor_already_is_use;
	$output = '';
	if ( false !== $vc_html_editor_already_is_use ) {
		$output .= WPB_Form_Field_Textarea::get( [
			'name'  => $settings['param_name'],
			'value' => $value,
			'id'    => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
			'class' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'], 'textarea', 'wpb-textarea' ),
		] );
		$output .= '<div class="updated"><p>' . sprintf( esc_html__( 'Field type is changed from "textarea_html" to "textarea", because it is already used by %s field. Textarea_html field\'s type can be used only once per shortcode.', 'js_composer' ), $vc_html_editor_already_is_use ) . '</p></div>';
	} else {
		// Generate editor ID for wp_editor.
		$editor_id = 'wpb_tinymce_' . esc_attr( $settings['param_name'] );

		$output .= WPB_Form_Field_Textarea_Html::get( [
			'name'          => $settings['param_name'],
			'value'         => $value,
			'id'            => $editor_id,
			'editor_class'  => 'wpb-textarea ' . esc_attr( $settings['param_name'] . ' ' . $settings['type'] ),
			'media_buttons' => true,
			'wpautop'       => false,
			'class'         => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], 'textarea_html_hidden', '', 'vc_textarea_html_content' ),
		] );
		$vc_html_editor_already_is_use = $settings['param_name'];
	}

	return $output;
}
