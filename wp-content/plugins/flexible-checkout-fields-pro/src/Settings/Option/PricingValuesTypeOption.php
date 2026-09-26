<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\PricingTab;

/**
 * {@inheritdoc}
 */
class PricingValuesTypeOption extends OptionAbstract {

	const FIELD_NAME = 'type';

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
		return PricingTab::TAB_NAME;
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
		return __( 'Price basis', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_values(): array {
		return [
			'fixed'                => __( 'Fixed', 'flexible-checkout-fields-pro' ),
			'percent_subtotal'     => __( 'Percentage of Subtotal (ex. VAT)', 'flexible-checkout-fields-pro' ),
			'percent_subtotal_tax' => __( 'Percentage of Subtotal (incl. VAT)', 'flexible-checkout-fields-pro' ),
			'percent_total'        => __( 'Percentage of Total', 'flexible-checkout-fields-pro' ),
		];
	}

	public function get_validation_rules(): array {
		return [
			'^.{1,}$' => __( 'This field is required.', 'flexible-checkout-fields-pro' ),
		];
	}
}
