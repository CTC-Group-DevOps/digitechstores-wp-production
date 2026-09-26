<?php

namespace WPDesk\FCF\Pro\Pricing\Field;

use WPDesk\FCF\Pro\Settings\Option\OptionsKeyOption;
use WPDesk\FCF\Pro\Settings\Option\OptionsOption;
use WPDesk\FCF\Pro\Settings\Option\OptionsValueOption;

/**
 * {@inheritdoc}
 */
class FieldRadio extends FieldAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_options_for_price_values(): array {
		return $this->get_field_options();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field_label_for_fee( $field_value, string $default_value ): string {
		$options = $this->get_field_options();
		return sprintf( '%s: %s', $this->get_field_label(), $options[ $field_value ] ?? $field_value );
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_price_in_field_label( array $args, string $value, string $label ): array {
		foreach ( $args[ OptionsOption::FIELD_NAME ] as $option_index => $option_data ) {
			if ( $option_data[ OptionsKeyOption::FIELD_NAME ] === $value ) {
				$args[ OptionsOption::FIELD_NAME ][ $option_index ][ OptionsValueOption::FIELD_NAME ] .= $label;
			}
		}

		return $args;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_valid_field_value( string $option_value, $current_value ): bool {
		return ( $option_value === $current_value );
	}
}
