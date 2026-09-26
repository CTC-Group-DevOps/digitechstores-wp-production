<?php

namespace WPDesk\FCF\Pro\Field\Attr;

use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Settings\Option\FirstWeekDayOption;

/**
 * {@inheritdoc}
 */
class WeekStartAttr implements Attr {

	const ATTR_NAME = 'data-week-start';

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
	public function get_value( array $field_data ): string {
		return $field_data[ FirstWeekDayOption::FIELD_NAME ] ?? FirstWeekDayOption::DEFAULT_FIRST_WEEK_DAY;
	}
}
