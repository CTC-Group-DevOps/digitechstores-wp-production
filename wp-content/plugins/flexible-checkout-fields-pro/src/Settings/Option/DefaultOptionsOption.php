<?php

namespace WPDesk\FCF\Pro\Settings\Option;

/**
 * {@inheritdoc}
 */
class DefaultOptionsOption extends DefaultOption {

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		return __( 'Default value', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label_tooltip(): string {
		return __( 'Enter the value of the option to be set as default.', 'flexible-checkout-fields-pro' );
	}
}
