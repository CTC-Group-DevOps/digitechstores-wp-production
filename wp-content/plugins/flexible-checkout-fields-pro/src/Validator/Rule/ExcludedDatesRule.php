<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Settings\Option\ExcludedDates;
use WPDesk\FCF\Pro\Validator\Error\ExcludedDateError;

/**
 * Checks whether an excluded date has been selected.
 */
class ExcludedDatesRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$excluded_dates = $field_data[ ExcludedDates::FIELD_NAME ] ?? null;
		if ( ! $excluded_dates ) {
			return null;
		}

		$excluded_dates = explode( ',', $excluded_dates );
		$disabled_dates = [];
		foreach ( $excluded_dates as $date ) {
			$disabled_dates[] = gmdate( 'Y-m-d', strtotime( $date ) );
		}

		$dates = ( $value ) ? explode( ',', $value ) : [];
		foreach ( $dates as $date ) {
			if ( in_array( gmdate( 'Y-m-d', strtotime( $date ) ), $disabled_dates, true ) ) {
				return new ExcludedDateError( $field_data, $value );
			}
		}

		return null;
	}
}
