<?php

namespace WPDesk\FCF\Pro\Field\Attr;

use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Settings\Option\DateFormatOption;
use WPDesk\FCF\Pro\Settings\Option\DaysBeforeOption;

/**
 * {@inheritdoc}
 */
class DateMinAttr implements Attr {

	const ATTR_NAME = 'data-date-min';

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
		$days_before     = $field_data[ DaysBeforeOption::FIELD_NAME ] ?? '';
		$date_format_php = DateFormatAttr::convert_date_format_for_php( $date_format );

		if ( $days_before === '' ) {
			$date_min = '';
		} else {
			$date_min = gmdate(
				$date_format_php,
				strtotime(
					sprintf(
						'%s %s%d day',
						\wp_date( 'Y-m-d H:i:s' ),
						(int) $days_before < 0 ? '+' : '-',
						\absint( $days_before )
					)
				)
			);
		}

		$date_min = apply_filters(
			'flexible_checkout_fields/field_args/date_min',
			$date_min,
			$field_data,
			$date_format_php
		);
		return ( $date_min !== '' ) ? $date_min : null;
	}
}
