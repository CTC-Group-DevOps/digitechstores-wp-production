<?php

namespace WPDesk\FCF\Pro\Field\Attr;

use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Settings\Option\DateFormatOption;
use WPDesk\FCF\Pro\Settings\Option\ExcludedDates;
use WPDesk\FCF\Pro\Settings\Option\TodayMaxHour;

/**
 * {@inheritdoc}
 */
class DatesDisabled implements Attr {

	const ATTR_NAME = 'data-dates-disabled';

	/**
	 * {@inheritdoc}
	 */
	public function is_available( array $field_data ): bool {
		return ( isset( $field_data['type'] ) && ( $field_data['type'] === DateType::FIELD_TYPE ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name(): string {
		return self::ATTR_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_value( array $field_data ) {
		$dates_excluded  = array_filter( explode( ',', $field_data[ ExcludedDates::FIELD_NAME ] ?? '' ) );
		$date_format     = ( $field_data[ DateFormatOption::FIELD_NAME ] ?? '' ) ?: DateFormatOption::DEFAULT_DATE_FORMAT;
		$date_format_php = DateFormatAttr::convert_date_format_for_php( $date_format );
		$today_max_hour  = $field_data[ TodayMaxHour::FIELD_NAME ] ?? '';

		foreach ( $dates_excluded as $date_index => $date ) {
			$dates_excluded[ $date_index ] = gmdate( $date_format_php, strtotime( $date ) );
		}
		if ( $today_max_hour && ( wp_date( 'H:i' ) > gmdate( 'H:i', strtotime( $today_max_hour ) ) ) ) {
			$dates_excluded[] = gmdate( $date_format_php, strtotime( wp_date( 'Y-m-d' ) ) );
		}

		$dates_excluded = apply_filters(
			'flexible_checkout_fields/field_args/dates_excluded',
			$dates_excluded,
			$field_data,
			$date_format_php
		);
		return ( $dates_excluded ) ? implode( ',', $dates_excluded ) : null;
	}
}
