<?php

namespace WPDesk\FCF\Pro\Field\Attr;

use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Settings\Option\SelectedDatesLimit;

/**
 * {@inheritdoc}
 */
class MaxDatesAttr implements Attr {

	const ATTR_NAME = 'data-max-dates';

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
		return $field_data[ SelectedDatesLimit::FIELD_NAME ] ?? SelectedDatesLimit::DEFAULT_DATES_LIMIT;
	}
}
