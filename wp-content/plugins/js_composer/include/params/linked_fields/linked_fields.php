<?php
/**
 * Linked Fields parameter type for element edit form.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! function_exists( 'vc_linked_fields_form_field' ) ) :
	/**
	 * Render linked_fields parameter field for element edit form.
	 *
	 * @param array  $settings Parameter settings from element config.
	 * @param string $value Current value of the parameter.
	 * @param string $tag Shortcode tag.
	 * @param string $param_id Unique parameter ID for label association.
	 * @return string HTML markup for the field.
	 * @since 9.0
	 */
	function vc_linked_fields_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$class = isset( $settings['class'] ) ? $settings['class'] : '';
		$heading = isset( $settings['heading'] ) ? $settings['heading'] : '';
		if ( ! isset( $settings['value'] ) || ! is_array( $settings['value'] ) ) {
			$settings['value'] = [
				'top' => '',
				'right' => '',
				'bottom' => '',
				'left' => '',
			];
		}

		$defaults = [ 'linked' => 'false' ];
		foreach ( array_keys( $settings['value'] ) as $key ) {
			$defaults[ $key ] = '';
		}

		$settings['settings']['units'] = WPB_Unit_Option::resolve_setting( $settings['settings']['units'] ?? false );
		$default_unit = WPB_Unit_Option::resolve_default_unit( $settings['settings'] );
		if ( '' !== $default_unit ) {
			$defaults['unit'] = $default_unit;
		}

		if ( ! empty( $settings['std'] ) ) {
			$parsed_std = vc_parse_multi_attribute( $settings['std'], $defaults );
			$defaults = array_merge( $defaults, $parsed_std );
		}

		$parsed_value = vc_parse_multi_attribute( $value, $defaults );

		return WPB_Form_Field_Linked_Fields::get(
			[
				'settings' => $settings,
				'param_id' => $param_id,
				'value' => $value,
				'class' => $class,
				'heading' => $heading,
				'parsed_value' => $parsed_value,
				'is_linked' => 'true' === $parsed_value['linked'],
			]
		);
	}
endif;
