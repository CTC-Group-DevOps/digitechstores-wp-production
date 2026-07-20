<?php

namespace WPDesk\FCF\Pro\Field\Attr;

use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Settings\Option\DateFormatOption;
use WPDesk\FCF\Pro\Settings\Option\DaysAfterOption;

/**
 * {@inheritdoc}
 */
class DateMaxAttr implements Attr {

	const ATTR_NAME = 'data-date-max';

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
		$date_format     = ( $field_data[ DateFormatOption::FIELD_NAME ] ?? '' ) ?: DateFormatOption::DEFAULT_DATE_FORMAT;
		$days_after      = $field_data[ DaysAfterOption::FIELD_NAME ] ?? '';
		$date_format_php = DateFormatAttr::convert_date_format_for_php( $date_format );

		$date_max = apply_filters(
			'flexible_checkout_fields/field_args/date_max',
			( $days_after !== '' )
				? gmdate( $date_format_php, strtotime( sprintf( '%s +%s day', wp_date( 'Y-m-d H:i:s' ), $days_after ) ) )
				: '',
			$field_data,
			$date_format_php
		);
		return ( $date_max !== '' ) ? $date_max : null;
	}
}
