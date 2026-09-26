<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Field\Attr\DateFormatAttr;
use WPDesk\FCF\Pro\Settings\Option\DateFormatOption;
use WPDesk\FCF\Pro\Settings\Option\DaysAfterOption;
use WPDesk\FCF\Pro\Validator\Error\DateTooLateError;

/**
 * Checks that the date is not later than allowed.
 */
class DaysAfterRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$days_after = $field_data[ DaysAfterOption::FIELD_NAME ] ?? '';
		if ( $days_after === '' ) {
			return null;
		}

		$valid_format = DateFormatAttr::convert_date_format_for_php( $field_data[ DateFormatOption::FIELD_NAME ] );
		$date_max     = gmdate( 'Ymd', strtotime( sprintf( '%s +%s day', wp_date( 'Y-m-d H:i:s' ), $days_after ) ) );
		$dates        = ( $value ) ? explode( ',', $value ) : [];
		foreach ( $dates as $date ) {
			if ( \DateTime::createFromFormat( $valid_format, $date )->format( 'Ymd' ) > $date_max ) {
				return new DateTooLateError( $field_data, $value );
			}
		}

		return null;
	}
}
