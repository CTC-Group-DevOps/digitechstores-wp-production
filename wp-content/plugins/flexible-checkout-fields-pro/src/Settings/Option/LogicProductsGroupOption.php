<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\LogicTab;

/**
 * {@inheritdoc}
 */
class LogicProductsGroupOption extends OptionAbstract {

	const FIELD_NAME = 'conditional_logic_group';

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
		return self::FIELD_TYPE_GROUP;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options_regexes_to_display(): array {
		return [
			LogicProductsEnabledOption::FIELD_NAME => '^1$',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_children(): array {
		return [
			LogicProductsGroupActionOption::FIELD_NAME   => new LogicProductsGroupActionOption(),
			LogicProductsGroupOperatorOption::FIELD_NAME => new LogicProductsGroupOperatorOption(),
		];
	}
}
