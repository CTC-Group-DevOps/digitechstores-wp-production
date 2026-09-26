<?php
/**
 * Param type 'dropdown'
 * Used to create a dropdown field
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WpbParamDropdown
 *
 * @since 9.0
 */
class WpbParamDropdown {
	/**
	 * Render param's HTML.
	 *
	 * @param array $settings Param settings.
	 * @param string $value Param value.
	 * @param string $tag WPBakery shortcode tag.
	 * @param string $param_id Param ID.
	 *
	 * @return string
	 */
	public function render( $settings, $value, $tag = '', $param_id = '' ) {
		if ( is_array( $value ) ) {
			$value = $value['value'] ?? array_shift( $value );
		}

		$css_option = $this->get_option_class( vc_get_dropdown_option( $settings, $value ) );

		return WPB_Form_Field_Dropdown::get( [ // nosemgrep.
			'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
			'classes' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'], $css_option ),
			'name' => $settings['param_name'],
			'options' => $this->get_options( $settings, $value ),
			'has_search' => ! empty( $settings['settings']['search'] ),
			'data_attributes' => [
				'option' => $css_option,
				'label' => $settings['heading'] ?? '',
			],
		] );
	}

	/**
	 * Get dropdown options.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @return array
	 *
	 * @since 9.0
	 */
	public function get_options( $settings, $value ) {
		$option_list = [];
		$option_value_list = empty( $settings['value'] ) ? [] : $settings['value'];
		foreach ( $option_value_list as $index => $data ) {
			$option_data = $this->get_option_data( $index, $data );

			if ( is_array( $option_data['option_value'] ) ) {
				$option_list[] = [
					'label' => $option_data['option_label'],
					'value' => $this->get_grouped_option_data( $option_data, $value ),
				];
			} else {
				$option_list[] = $this->get_regular_option_data( $option_data, $value );
			}
		}

		return $option_list;
	}

	/**
	 * Get data for grouped dropdown option.
	 *
	 * @since 9.0
	 * @param array $option_data
	 * @param mixed $value
	 * @return array
	 */
	public function get_grouped_option_data( $option_data, $value ) {
		$value_string = (string) $value;
		$sub_option_list = [];
		foreach ( $option_data['option_value'] as $sub_option_data ) {
			$sub_option_data['class'] = $this->get_option_class( $sub_option_data['value'] );
			if ( '' === $value ) {
				if ( empty( $sub_option_data['selected'] ) ) {
					$sub_option_data['selected'] = false;
				}
			} else {
				$sub_option_data['selected'] = (string) $sub_option_data['value'] === $value_string;
			}
			$sub_option_list[] = $sub_option_data;
		}

		return $sub_option_list;
	}

	/**
	 * Get data for regular dropdown option (non-grouped).
	 *
	 * @since 9.0
	 * @param array $option_data
	 * @param mixed $value
	 * @return array
	 */
	public function get_regular_option_data( $option_data, $value ) {
		$value_string = (string) $value;

		$list_data = [
			'value' => $option_data['option_value'],
			'label' => $option_data['option_label'],
		];
		$list_data['class'] = $this->get_option_class( $option_data['option_value'] );
		$list_data['selected'] = '' !== $value && (string) $option_data['option_value'] === $value_string;

		return $list_data;
	}

	/**
	 * Get option class.
	 *
	 * @param string $option_value
	 * @return string
	 *
	 * @since 9.0
	 */
	public function get_option_class( $option_value ) {
		return str_replace( '#', 'hash-', $option_value );
	}

	/**
	 * Get dropdown option value and label.
	 *
	 * @param mixed $options_key
	 * @param mixed $options_data
	 * @return array
	 *
	 * @since 9.0
	 */
	public function get_option_data( $options_key, $options_data ) {
		if ( ! is_numeric( $options_key ) ) {
			return [
				'option_value' => $options_data,
				'option_label' => $options_key,
			];
		}

		if ( is_string( $options_data ) || is_numeric( $options_data ) ) {
			return [
				'option_value' => $options_data,
				'option_label' => $options_data,
			];
		}

		if ( is_array( $options_data ) ) {
			return $this->parse_option_data( $options_data );
		}

		return [
			'option_value' => $options_data,
			'option_label' => $options_key,
		];
	}

	/**
	 * Parse option data array for dropdown.
	 *
	 * @param array $options_data
	 * @return array
	 *
	 * @since 9.0
	 */
	protected function parse_option_data( $options_data ) {
		// Grouped select associative arrays ['value' => $val, 'label' => $label].
		if ( isset( $options_data['value'] ) && is_array( $options_data['value'] ) ) {
			return [
				'option_value' => $options_data['value'],
				'option_label' => $options_data['label'] ?? '',
			];
		}

		// Support indexed arrays [$value, $label] for backwards compatibility.
		$option_label = $options_data['label'] ?? array_pop( $options_data );
		$option_value = $options_data['value'] ?? array_pop( $options_data ) ?? $option_label;

		return [
			'option_value' => $option_value,
			'option_label' => $option_label,
		];
	}
}
