<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\PricingTab;

/**
 * {@inheritdoc}
 */
class PricingValuesTaxesOption extends OptionAbstract {

	const FIELD_NAME = 'tax_class';

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
		return __( 'Tax class', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options_regexes_to_display(): array {
		return [
			PricingValuesValueOption::FIELD_NAME => '^([1-9]|0\.[0-9])',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_values(): array {
		$values = [];
		if ( ! wc_tax_enabled() ) {
			return $values;
		}

		return array_merge(
			$values,
			[
				'standard' => __( 'Standard rates', 'flexible-checkout-fields-pro' ),
			],
			array_combine( \WC_Tax::get_tax_class_slugs(), \WC_Tax::get_tax_classes() )
		);
	}
}
