<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Field\Attr\DateFormatAttr;
use WPDesk\FCF\Pro\Settings\Option\DateFormatOption;
use WPDesk\FCF\Pro\Validator\Error\InvalidDateFormatError;

/**
 * Checks that the date format is valid.
 */
class DateFormatRule implements ValidatorRule {

	/**
	 * Be aware of potential gotcha as we suggest our users to use simplified date format which
	 * consists only of 3 characters (y, m, d) and we convert it to full date format, but format
	 * coming from configuration is not validated and can be anything, what converts to PHP date
	 * format. For example "U" date format will pass without any format conversion, even though
	 * end-user will not be able to enter unix timestamp in the date field on frontend, what
	 * eventually results in validation error.
	 *
	 * @param string                      $value      Value to validate.
	 * @param array{date_format?: string} $field_data Field data.
	 *
	 * @see DateFormatAttr::convert_date_format_for_php()
	 */
	public function validate_value( string $value, array $field_data ) {
		$date_format = $field_data[ DateFormatOption::FIELD_NAME ] ?? null;
		if ( ! $date_format ) {
			return null;
		}

		$valid_format = DateFormatAttr::convert_date_format_for_php( $date_format );
		$dates        = $value ? explode( ',', $value ) : [];
		foreach ( $dates as $date_string ) {
			$date = \DateTime::createFromFormat( $valid_format, $date_string );
			if ( $date === false || $date->format( $valid_format ) !== $date_string ) {
				return new InvalidDateFormatError( $field_data, $value );
			}
		}

		return null;
	}
}
