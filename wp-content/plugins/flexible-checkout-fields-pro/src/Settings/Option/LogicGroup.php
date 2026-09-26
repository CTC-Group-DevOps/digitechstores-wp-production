<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\LogicTab;

/**
 * {@inheritdoc}
 */
class LogicGroup extends OptionAbstract {

	const FIELD_NAME = 'conditional_logic';

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
		return self::FIELD_TYPE_REPEATER_GROUP;
	}

	/**
	 * Returns label of option row (for Repeater field).
	 *
	 * @return string Option row label.
	 */
	public function get_option_row_label(): string {
		/* translators: %s: rule index */
		return __( 'Group %s', 'flexible-checkout-fields-pro' );
	}

	public function get_default_value() {
		return [
			[
				LogicGroupAction::FIELD_NAME   => '',
				LogicGroupRules::FIELD_NAME    => [],
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_children(): array {
		return [
			LogicGroupAction::FIELD_NAME   => new LogicGroupAction(),
			LogicGroupRules::FIELD_NAME    => new LogicGroupRules(),
		];
	}
}
