<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Free\Settings\Option\RequiredOption;
use WPDesk\FCF\Pro\Validator\Error\FieldRequiredError;

/**
 * Checks that the given field value as an array is not empty.
 */
class ArrayValueRequiredRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		if ( ! $field_data[ RequiredOption::FIELD_NAME ] || ( $value === '[]' ) || ! $value ) {
			return null;
		}

		$field_value  = json_decode( $value, true );
		$field_values = ( is_array( $field_value ) ) ? array_filter( $field_value ) : [];
		if ( $field_values ) {
			return null;
		}

		return new FieldRequiredError( $field_data, $value );
	}
}
