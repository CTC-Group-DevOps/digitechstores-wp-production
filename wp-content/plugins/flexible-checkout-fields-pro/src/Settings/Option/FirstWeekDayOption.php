<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\AdvancedTab;

/**
 * {@inheritdoc}
 */
class FirstWeekDayOption extends OptionAbstract {

	const FIELD_NAME             = 'first_week_day';
	const DEFAULT_FIRST_WEEK_DAY = '0';

	/**
	 * {@inheritdoc}
	 */
	public function get_option_name(): string {
		return self::FIELD_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_tab(): string {
		return AdvancedTab::TAB_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_SELECT;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		return __( 'First day of week', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_validation_rules(): array {
		return [
			'^.{1,}$' => __( 'This field is required.', 'flexible-checkout-fields-pro' ),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_values(): array {
		return [
			'0' => __( 'Sunday', 'flexible-checkout-fields-pro' ),
			'1' => __( 'Monday', 'flexible-checkout-fields-pro' ),
			'2' => __( 'Tuesday', 'flexible-checkout-fields-pro' ),
			'3' => __( 'Wednesday', 'flexible-checkout-fields-pro' ),
			'4' => __( 'Thursday', 'flexible-checkout-fields-pro' ),
			'5' => __( 'Friday', 'flexible-checkout-fields-pro' ),
			'6' => __( 'Saturday', 'flexible-checkout-fields-pro' ),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return self::DEFAULT_FIRST_WEEK_DAY;
	}
}
