<?php

namespace WPDesk\FCF\Pro\Field\Attr;

use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Settings\Option\ExcludedWeekDays;

/**
 * {@inheritdoc}
 */
class DaysDisabled implements Attr {

	const ATTR_NAME = 'data-days-disabled';

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
		$excluded_days = $field_data[ ExcludedWeekDays::FIELD_NAME ] ?? [];
		return ( $excluded_days ) ? implode( ',', $excluded_days ) : null;
	}
}
