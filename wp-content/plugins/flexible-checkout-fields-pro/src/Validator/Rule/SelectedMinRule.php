<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Settings\Option\SelectedMinOption;
use WPDesk\FCF\Pro\Validator\Error\SelectedMinError;

/**
 * Checks if the number of selected options is allowed.
 */
class SelectedMinRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$field_values = json_decode( $value, true );
		$values_min   = $field_data[ SelectedMinOption::FIELD_NAME ] ?? null;
		if ( ! $values_min || ( count( $field_values ) >= $values_min ) ) {
			return null;
		}

		return new SelectedMinError( $field_data, $value );
	}
}
