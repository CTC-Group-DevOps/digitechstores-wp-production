<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\LogicTab;

/**
 * {@inheritdoc}
 */
class LogicGroupAction extends OptionAbstract {

	const FIELD_NAME = 'action';

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
		return LogicTab::TAB_NAME;
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
		return __( 'What to do?', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_values(): array {
		return [
			''         => __( 'No action', 'flexible-checkout-fields-pro' ),
			'show'     => __( 'Show this field', 'flexible-checkout-fields-pro' ),
			'hide'     => __( 'Hide this field', 'flexible-checkout-fields-pro' ),
			'required' => __( 'Set as required', 'flexible-checkout-fields-pro' ),
		];
	}

	public function get_default_value() {
		return '';
	}
}
