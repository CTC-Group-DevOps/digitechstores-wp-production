<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Settings\Option\HourMinOption;
use WPDesk\FCF\Pro\Validator\Error\HourMinError;

/**
 * Checks that the time is not earlier than allowed.
 */
class HourMinRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$hour_min = $field_data[ HourMinOption::FIELD_NAME ] ?? '';
		if ( ( $hour_min === '' ) || ( $value === '' ) ) {
			return null;
		}

		$value_hour = date( 'H:i', strtotime( $value ) );
		if ( $value_hour >= $hour_min ) {
			return null;
		}

		return new HourMinError( $field_data, $value );
	}
}
