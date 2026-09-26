<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\DefaultOption as HiddenDefaultOption;

/**
 * {@inheritdoc}
 */
class DefaultOption extends HiddenDefaultOption {

	/**
	 * {@inheritdoc}
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_TEXT;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		return __( 'Default value', 'flexible-checkout-fields-pro' );
	}
}
