<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\AdvancedTab;

/**
 * {@inheritdoc}
 */
class ExcludedWeekDays extends OptionAbstract {

	const FIELD_NAME = 'excluded_week_days';

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
		return self::FIELD_TYPE_SELECT_MULTI;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		return __( 'Excluded days of week', 'flexible-checkout-fields-pro' );
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
}
