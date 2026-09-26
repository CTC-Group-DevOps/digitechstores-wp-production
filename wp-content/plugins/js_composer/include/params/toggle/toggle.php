<?php
/**
 * Toggle parameter type for element edit form.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! function_exists( 'vc_toggle_form_field' ) ) :
	/**
	 * Render toggle parameter field for element edit form.
	 *
	 * @param array  $settings Parameter settings from element config.
	 * @param string $value Current value of the parameter.
	 * @param string $tag Shortcode tag.
	 * @param string $param_id Unique parameter ID for label association.
	 * @return string HTML markup for the toggle field.
	 * @since 9.0
	 */
	function vc_toggle_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$checked_value = isset( $settings['value'] ) ? ( is_array( $settings['value'] ) ? (string) current( $settings['value'] ) : (string) $settings['value'] ) : 'true';
		$is_checked = $checked_value === (string) $value;

		return vc_get_template( 'params/toggle/template.php', [
			'settings' => $settings,
			'value' => $value,
			'param_id' => $param_id,
			'class' => isset( $settings['class'] ) ? $settings['class'] : '',
			'is_checked' => $is_checked,
			'checked_value' => $checked_value,
		] );
	}
endif;
