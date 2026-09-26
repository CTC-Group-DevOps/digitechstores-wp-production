<?php
/**
 * Button group parameter type for element edit form.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! function_exists( 'vc_button_group_detect_label_type' ) ) :
	/**
	 * Detect the display type of button group label.
	 *
	 * @param string $label The label value to analyze.
	 * @return array Array with 'type' (image|icon|text) and 'icon_class' keys.
	 * @since 9.0
	 */
	function vc_button_group_detect_label_type( $label ) {
		$result = [
			'type' => 'text',
			'icon_class' => '',
		];

		if ( empty( $label ) ) {
			return $result;
		}

		// Check for WPBakery font icons.
		if ( strpos( $label, 'vc-c-' ) === 0 ) {
			$result['type'] = 'icon';
			$result['icon_class'] = 'vc-composer-icon ' . $label;
			return $result;
		}

		// Check for WordPress dashicons.
		if ( strpos( $label, 'dashicons-' ) === 0 ) {
			$result['type'] = 'icon';
			$result['icon_class'] = 'dashicons ' . $label;
			return $result;
		}

		// Check for image file by extension.
		$image_mimes = [
			'svg'  => 'image/svg+xml',
			'png'  => 'image/png',
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'webp' => 'image/webp',
		];
		$filetype = wp_check_filetype( $label, $image_mimes );
		if ( ! empty( $filetype['type'] ) ) {
			$result['type'] = 'image';
			return $result;
		}

		return $result;
	}
endif;

if ( ! function_exists( 'vc_button_group_process_options' ) ) :
	/**
	 * Process button group options to include display type information.
	 *
	 * @param array $options Raw options array from settings.
	 * @return array Processed options with type and icon_class added.
	 * @since 9.0
	 */
	function vc_button_group_process_options( $options ) {
		$processed = [];
		foreach ( $options as $option_value => $option_data ) {
			$label = $option_data['label'] ?? '';
			$title = $option_data['title'] ?? $option_value;
			$type_info = vc_button_group_detect_label_type( $label );

			$processed[ $option_value ] = [
				'label' => $label,
				'title' => $title,
				'type' => $type_info['type'],
				'icon_class' => $type_info['icon_class'],
			];
		}
		return $processed;
	}
endif;

if ( ! function_exists( 'vc_button_group_form_field' ) ) :
	/**
	 * Render button group parameter field for element edit form.
	 *
	 * @param array  $settings Parameter settings from element config.
	 * @param string $value Current value of the parameter.
	 * @param string $tag Shortcode tag.
	 * @param string $param_id Unique parameter ID for label association.
	 * @return string HTML markup for the button group field.
	 * @since 9.0
	 */
	function vc_button_group_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$class = $settings['class'] ?? '';
		$options = isset( $settings['value'] ) && is_array( $settings['value'] ) ? $settings['value'] : [
			'option_1' => [
				'label' => esc_html__( 'Option 1', 'js_composer' ),
			],
			'option_2' => [
				'label' => esc_html__( 'Option 2', 'js_composer' ),
			],
		];
		$heading = $settings['heading'] ?? '';
		$current_value = '' !== $value ? $value : ( $settings['std'] ?? '' );

		// If no current value and options exist, default to first option.
		if ( '' === $current_value && ! empty( $options ) ) {
			$option_keys = array_keys( $options );
			$current_value = reset( $option_keys );
		}

		// Get icon size from settings (e.g., '20px', '1.5em').
		$icon_size = $settings['settings']['icon_size'] ?? '';

		$input_classes = wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] );
		$id = wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] );

		return WPB_Form_Field_Button_Group::get(
			[
				'id' => $id,
				'name' => $settings['param_name'],
				'class' => $class,
				'input_classes' => $input_classes,
				'options' => $options,
				'heading' => $heading,
				'current_value' => (string) $current_value,
				'icon_size' => $icon_size,
			]
		);
	}
endif;
