<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Settings\Option\SelectedDatesLimit;
use WPDesk\FCF\Pro\Validator\Error\DatesLimitError;

/**
 * Checks that the number of selected dates does not exceed the limit.
 */
class DatesLimitRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$dates_limit = $field_data[ SelectedDatesLimit::FIELD_NAME ] ?? null;
		if ( ! $dates_limit ) {
			return null;
		}

		$dates = ( $value ) ? explode( ',', $value ) : [];
		if ( count( $dates ) > $dates_limit ) {
			return new DatesLimitError( $field_data, $value );
		}

		return null;
	}
}
