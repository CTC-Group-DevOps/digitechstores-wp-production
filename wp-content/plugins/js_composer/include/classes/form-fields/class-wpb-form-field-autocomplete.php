<?php
/**
 * Autocomplete form field class.
 *
 * Handles autocomplete-specific logic and rendering.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Autocomplete
 *
 * Autocomplete form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Autocomplete extends WPB_Form_Field_Abstract {

	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'autocomplete';
	}

	/**
	 * Get default values for autocomplete arguments.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	protected function get_defaults() {
		return [
			'id'                => '',
			'name'              => '',
			'value'             => '',
			'tag'               => '',
			'param_settings'    => [],
			'settings'          => [],
			'is_inline'         => false,
			'is_multiple'       => false,
			'placeholder'       => esc_attr__( 'Click here and start typing...', 'js_composer' ),
			'settings_json'     => '',
			'parsed_values'     => [],
			'classes'           => '',
		];
	}

	/**
	 * Prepare arguments for template rendering.
	 *
	 * @since 9.0
	 *
	 * @param array $args User-provided arguments.
	 * @return array Prepared arguments for template.
	 */
	protected function prepare_args( array $args ) {
		$args = parent::prepare_args( $args );

		// Determine inline and multiple settings.
		$args['is_inline'] = isset( $args['settings']['display_inline'] ) && true === $args['settings']['display_inline'];
		$args['is_multiple'] = isset( $args['settings']['multiple'] ) && true === $args['settings']['multiple'];

		// Prepare settings JSON.
		$args['settings_json'] = ( ! empty( $args['settings'] ) ) ? wp_json_encode( $args['settings'] ) : '';

		// Parse values for display.
		$args['parsed_values'] = $this->parse_values( $args );

		return $args;
	}

	/**
	 * Parse and prepare values for autocomplete options.
	 *
	 * @since 9.0
	 *
	 * @param array $args Field arguments.
	 * @return array Parsed values with labels.
	 */
	protected function parse_values( array $args ) {
		$parsed_values = [];

		if ( ! isset( $args['value'] ) || strlen( $args['value'] ) === 0 ) {
			return $parsed_values;
		}

		$values = explode( ',', $args['value'] );

		foreach ( $values as $val ) {
			$parsed_values = $this->parse_single_value( $val, $args, $parsed_values );
		}

		return $parsed_values;
	}

	/**
	 * Parse a single value for autocomplete.
	 *
	 * @since 9.0
	 *
	 * @param string $val Single value to parse.
	 * @param array $args Field arguments.
	 * @param array $parsed_values Existing parsed values.
	 * @return array Updated parsed values.
	 */
	protected function parse_single_value( $val, array $args, array $parsed_values ) {
		$value = [
			'value' => trim( $val ),
			'label' => trim( $val ),
		];

		// Check if we have predefined values in settings.
		if ( isset( $args['settings']['values'] ) && ! empty( $args['settings']['values'] ) ) {
			$value = $this->get_label_from_predefined_values( $value, $args['settings']['values'] );
		} else {
			// Apply filter for dynamic rendering.
			$value = apply_filters(
				'vc_autocomplete_' . $args['tag'] . '_' . $args['name'] . '_render',
				$value,
				$args['param_settings'],
				$args['tag']
			);
		}

		if ( is_array( $value ) && isset( $value['value'], $value['label'] ) ) {
			$parsed_values[] = $value;
		}

		return $parsed_values;
	}

	/**
	 * Get label from predefined values.
	 *
	 * @since 9.0
	 *
	 * @param array $value Current value array.
	 * @param array $predefined_values Predefined values list.
	 * @return array Updated value array with label.
	 */
	protected function get_label_from_predefined_values( array $value, array $predefined_values ) {
		foreach ( $predefined_values as $data ) {
			if ( trim( $data['value'] ) === trim( $value['value'] ) ) {
				$value['label'] = $data['label'];
				break;
			}
		}

		return $value;
	}
}
