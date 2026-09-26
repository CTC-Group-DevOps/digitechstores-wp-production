<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Settings\Option\SelectedMaxOption;
use WPDesk\FCF\Pro\Validator\Error\SelectedMaxError;

/**
 * Checks if the number of selected options is allowed.
 */
class SelectedMaxRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$field_values = json_decode( $value, true );
		$values_max   = $field_data[ SelectedMaxOption::FIELD_NAME ] ?? null;
		if ( ! $values_max || ( count( $field_values ) <= $values_max ) ) {
			return null;
		}

		return new SelectedMaxError( $field_data, $value );
	}
}
