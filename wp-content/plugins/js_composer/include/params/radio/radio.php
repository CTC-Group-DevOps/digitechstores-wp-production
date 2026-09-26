<?php
/**
 * Radio parameter type for element edit form.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! function_exists( 'vc_radio_form_field' ) ) :
	/**
	 * Render radio parameter field for element edit form.
	 *
	 * @param array  $settings Parameter settings from element config.
	 * @param string $value Current value of the parameter.
	 * @param string $tag Shortcode tag.
	 * @param string $param_id Unique parameter ID for label association.
	 * @return string HTML markup for the radio field.
	 * @since 9.0
	 */
	function vc_radio_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$class = isset( $settings['class'] ) ? $settings['class'] : '';
		$options = isset( $settings['value'] ) && is_array( $settings['value'] ) ? $settings['value'] : [
			esc_html__( 'Option 1', 'js_composer' ) => 'option_1',
			esc_html__( 'Option 2', 'js_composer' ) => 'option_2',
		];
		$heading = isset( $settings['heading'] ) ? $settings['heading'] : '';
		$direction = isset( $settings['settings']['direction'] ) && 'vertical' === $settings['settings']['direction'] ? 'vertical' : 'horizontal';
		$current_value = '' !== $value ? $value : ( isset( $settings['std'] ) ? $settings['std'] : '' );

		return vc_get_template( 'params/radio/template.php', [
			'settings' => $settings,
			'value' => $value,
			'param_id' => $param_id,
			'class' => $class,
			'options' => $options,
			'heading' => $heading,
			'current_value' => $current_value,
			'direction' => $direction,
		] );
	}
endif;
