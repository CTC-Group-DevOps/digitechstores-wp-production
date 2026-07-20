<?php

namespace WPDesk\FCF\Pro\Validator\Rule;

use WPDesk\FCF\Pro\Settings\Option\TodayMaxHour;
use WPDesk\FCF\Pro\Validator\Error\UnavailableTodayDateError;

/**
 * Checks if selected today date could be selected.
 */
class TodayMaxHourRule implements ValidatorRule {

	/**
	 * {@inheritdoc}
	 */
	public function validate_value( string $value, array $field_data ) {
		$today_max_hour = $field_data[ TodayMaxHour::FIELD_NAME ] ?? null;
		if ( ! $today_max_hour || ( wp_date( 'H:i' ) <= gmdate( 'H:i', strtotime( $today_max_hour ) ) ) ) {
			return null;
		}

		$date_today = wp_date( 'Ymd' );
		$dates      = ( $value ) ? explode( ',', $value ) : [];
		foreach ( $dates as $date ) {
			if ( gmdate( 'Ymd', strtotime( $date ) ) === $date_today ) {
				return new UnavailableTodayDateError( $field_data, $value );
			}
		}

		return null;
	}
}
