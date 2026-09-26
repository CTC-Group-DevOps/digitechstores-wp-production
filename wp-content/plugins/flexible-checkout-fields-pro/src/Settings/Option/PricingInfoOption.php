<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\PricingTab;

/**
 * {@inheritdoc}
 */
class PricingInfoOption extends OptionAbstract {

	const FIELD_NAME = 'pricing_info';

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
		return self::FIELD_TYPE_INFO;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		$url = esc_url( apply_filters( 'flexible_checkout_fields/short_url', '#', 'fcf-settings-field-tab-pricing-docs' ) );
		return sprintf(
		/* translators: %1$s: anchor opening tag, %2$s: anchor closing tag */
			__( 'The impact on the final purchase amount depends on the selected price type and its value. %1$sRead more%2$s', 'flexible-checkout-fields-pro' ),
			'<a href="' . $url . '" target="_blank" class="fcfArrowLink">',
			'</a>'
		);
	}
}
