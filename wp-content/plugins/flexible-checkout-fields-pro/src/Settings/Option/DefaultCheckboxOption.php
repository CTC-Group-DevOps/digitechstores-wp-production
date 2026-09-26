<?php

namespace WPDesk\FCF\Pro\Settings\Option;

/**
 * {@inheritdoc}
 */
class DefaultCheckboxOption extends DefaultOption {

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
		return __( 'Enter "Yes" if the checkbox is to be selected by default.', 'flexible-checkout-fields-pro' );
	}
}
