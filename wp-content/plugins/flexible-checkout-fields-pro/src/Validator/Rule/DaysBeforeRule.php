<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Field\Attr\DateFormatAttr;
use WPDesk\FCF\Pro\Settings\Option\DateFormatOption;
use WPDesk\FCF\Pro\Settings\Option\DaysBeforeOption;
use WPDesk\FCF\Pro\Validator\Error\DateTooEarlyError;

/**
 * Checks that the date is not earlier than allowed.
 */
class DaysBeforeRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$days_before = $field_data[ DaysBeforeOption::FIELD_NAME ] ?? '';
		if ( $days_before === '' ) {
			return null;
		}

		$valid_format = DateFormatAttr::convert_date_format_for_php( $field_data[ DateFormatOption::FIELD_NAME ] );
		$date_min     = gmdate(
			'Ymd',
			strtotime(
				sprintf(
					'%s %s%d day',
					\wp_date( 'Y-m-d H:i:s' ),
					(int) $days_before < 0 ? '+' : '-',
					\absint( $days_before )
				)
			)
		);
		$dates        = ( $value ) ? explode( ',', $value ) : [];
		foreach ( $dates as $date ) {
			if ( \DateTime::createFromFormat( $valid_format, $date )->format( 'Ymd' ) < $date_min ) {
				return new DateTooEarlyError( $field_data, $value );
			}
		}

		return null;
	}
}
