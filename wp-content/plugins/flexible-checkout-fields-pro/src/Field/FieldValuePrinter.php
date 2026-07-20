<?php

namespace WPDesk\FCF\Pro\Field;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FCF\Pro\Field\Type\MultiCheckboxType;
use WPDesk\FCF\Pro\Field\Type\MultiSelectType;
use WPDesk\FCF\Pro\Field\Type\RadioColorsType;
use WPDesk\FCF\Pro\Field\Type\RadioImagesType;
use WPDesk\FCF\Pro\Field\Type\RadioType;
use WPDesk\FCF\Pro\Field\Type\SelectType;

/**
 * .
 */
class FieldValuePrinter implements Hookable {

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_action( 'flexible_checkout_fields_print_value', [ $this, 'print_field_value' ], 10, 2 );
		add_action( 'flexible_checkout_fields_user_meta_display_value', [ $this, 'print_field_value' ], 10, 2 );
	}

	/**
	 * @param string $value      .
	 * @param array  $field_data .
	 *
	 * @return string
	 */
	public function print_field_value( $value, $field_data ) {
		switch ( $field_data['type'] ?? '' ) {
			case SelectType::FIELD_TYPE:
			case RadioType::FIELD_TYPE:
			case RadioImagesType::FIELD_TYPE:
			case RadioColorsType::FIELD_TYPE:
				$field_values = ( $value !== '' ) ? [ $value ] : [];
				$options      = $this->get_options( $field_data );
				break;
			case MultiCheckboxType::FIELD_TYPE:
			case MultiSelectType::FIELD_TYPE:
				$field_values = json_decode( $value, true ) ?: [];
				$options      = $this->get_options( $field_data );
				break;
			default:
				return $value;
		}

		$values = [];
		foreach ( $field_values as $field_value ) {
			if ( $field_value === '' ) {
				continue;
			}
			$values[] = $options[ $field_value ] ?? $field_value;
		}

		return implode( ', ', $values );
	}

	/**
	 * @param array $field_data .
	 *
	 * @return array
	 */
	private function get_options( $field_data ): array {
		$options = [];
		foreach ( $field_data['options'] as $option_data ) {
			$options[ $option_data['key'] ] = $option_data['value'];
		}

		return $options;
	}
}
