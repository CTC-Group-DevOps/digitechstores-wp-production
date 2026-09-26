<?php
/**
 * Unit option utilities for CSS value parsing.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Unit_Option
 *
 * Provides static methods for handling CSS unit parsing, extraction,
 * and resolution for number and linked_fields param types.
 *
 * @since 9.0
 */
class WPB_Unit_Option {

	/**
	 * Extract the numeric portion from a CSS value string.
	 *
	 * Given a value like '80px' or '2.5rem', returns '80' or '2.5'.
	 * If the value is already numeric or empty, returns it as-is.
	 *
	 * @param string $value The CSS value string.
	 * @return string The numeric portion.
	 * @since 9.0
	 */
	public static function extract_numeric_value( $value ) {
		if ( '' === $value || is_null( $value ) ) {
			return '';
		}

		if ( preg_match( '/^(-?\d*\.?\d+)/', (string) $value, $matches ) ) {
			return $matches[1];
		}

		return $value;
	}

	/**
	 * Extract the unit suffix from a CSS value string.
	 *
	 * Given '80px', returns 'px'. Given '2.5rem', returns 'rem'.
	 * Returns empty string if no unit found.
	 *
	 * @param string $value The CSS value string.
	 * @return string The unit suffix.
	 * @since 9.0
	 */
	public static function extract_unit( $value ) {
		if ( '' === $value || is_null( $value ) ) {
			return '';
		}

		if ( preg_match( '/^-?\d*\.?\d+([a-zA-Z%]+)$/', (string) $value, $matches ) ) {
			return $matches[1];
		}

		return '';
	}

	/**
	 * Parse a CSS value into its numeric and unit parts for a given units setting.
	 *
	 * Returns an array with 'value' (numeric string or original value),
	 * 'units' (resolved units array), and 'selected_unit'.
	 *
	 * @param mixed  $value The CSS value string (e.g. '80px').
	 * @param mixed  $units_setting The units setting (true, array, or falsy).
	 * @return array {value: string, units: array, selected_unit: string}.
	 * @since 9.0
	 */
	public static function parse_value( $value, $units_setting ) {
		$units = self::resolve_setting( $units_setting );
		$selected_unit = '';

		if ( ! empty( $units ) ) {
			$unit_from_value = self::extract_unit( $value );
			$selected_unit = in_array( $unit_from_value, $units, true ) ? $unit_from_value : $units[0];
			$value = self::extract_numeric_value( $value );
		}

		return [
			'value' => $value,
			'units' => $units,
			'selected_unit' => $selected_unit,
		];
	}

	/**
	 * Resolve the default unit for a linked_fields parameter.
	 *
	 * Returns the explicit default_unit if set, or the first available unit.
	 * Returns empty string if no units are configured.
	 *
	 * @param array $settings The parameter settings array.
	 * @return string The default unit string.
	 * @since 9.0
	 */
	public static function resolve_default_unit( $settings ) {
		$units = self::resolve_setting( $settings['units'] ?? false );

		if ( empty( $units ) ) {
			return '';
		}

		return $settings['default_unit'] ?? $units[0];
	}

	/**
	 * Resolve the units setting to an array.
	 *
	 * Accepts true (returns default units), an array (returns as-is),
	 * or any falsy value (returns empty array).
	 *
	 * @param mixed $units The units setting value.
	 * @return array Resolved array of unit strings.
	 * @since 9.0
	 */
	public static function resolve_setting( $units ) {
		if ( true === $units ) {
			return vc_get_shared( 'default-units' );
		}

		if ( is_array( $units ) ) {
			return $units;
		}

		return [];
	}
}
