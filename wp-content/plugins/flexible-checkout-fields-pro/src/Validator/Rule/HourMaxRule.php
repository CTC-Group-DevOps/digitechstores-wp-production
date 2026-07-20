<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Settings\Option\HourMaxOption;
use WPDesk\FCF\Pro\Validator\Error\HourMaxError;

/**
 * Checks that the time is not later than allowed.
 */
class HourMaxRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$hour_max = $field_data[ HourMaxOption::FIELD_NAME ] ?? '';
		if ( ( $hour_max === '' ) || ( $value === '' ) ) {
			return null;
		}

		$value_hour = date( 'H:i', strtotime( $value ) );
		if ( $value_hour <= $hour_max ) {
			return null;
		}

		return new HourMaxError( $field_data, $value );
	}
}
