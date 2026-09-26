<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Field\Attr\DateFormatAttr;
use WPDesk\FCF\Pro\Settings\Option\DateFormatOption;
use WPDesk\FCF\Pro\Settings\Option\ExcludedWeekDays;
use WPDesk\FCF\Pro\Validator\Error\ExcludedWeekDayError;

/**
 * Checks if the date is not included in the excluded days of the week.
 */
class ExcludedWeekDaysRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$days_excluded = $field_data[ ExcludedWeekDays::FIELD_NAME ] ?? null;
		if ( ! $days_excluded ) {
			return null;
		}

		$valid_format = DateFormatAttr::convert_date_format_for_php( $field_data[ DateFormatOption::FIELD_NAME ] );
		$dates        = ( $value ) ? explode( ',', $value ) : [];
		foreach ( $dates as $date ) {
			if ( in_array( \DateTime::createFromFormat( $valid_format, $date )->format( 'w' ), $days_excluded, true ) ) {
				return new ExcludedWeekDayError( $field_data, $value );
			}
		}

		return null;
	}
}
