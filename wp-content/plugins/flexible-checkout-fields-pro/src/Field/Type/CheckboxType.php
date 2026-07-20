<?php

namespace WPDesk\FCF\Pro\Field\Type;

use WPDesk\FCF\Free\Field\Type\CheckboxType as DefaultCheckboxType;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;
use WPDesk\FCF\Free\Settings\Tab\LogicTab;
use WPDesk\FCF\Free\Settings\Tab\PricingTab;
use WPDesk\FCF\Pro\Settings\Option\DefaultOption;
use WPDesk\FCF\Pro\Settings\Option\LogicGroup;
use WPDesk\FCF\Pro\Settings\Option\PricingEnabledOption;
use WPDesk\FCF\Pro\Settings\Option\PricingInfoOption;
use WPDesk\FCF\Pro\Settings\Option\PricingValueOption;

/**
 * {@inheritdoc}
 */
class CheckboxType extends DefaultCheckboxType {

	/**
	 * {@inheritdoc}
	 */
	public function get_options_objects(): array {
		$options = array_merge(
			parent::get_options_objects(),
			[
				LogicTab::TAB_NAME   => [
					LogicGroup::FIELD_NAME => new LogicGroup(),
				],
				PricingTab::TAB_NAME => [
					PricingEnabledOption::FIELD_NAME => new PricingEnabledOption(),
					PricingValueOption::FIELD_NAME   => new PricingValueOption(),
					PricingInfoOption::FIELD_NAME    => new PricingInfoOption(),
				],
			]
		);

		$options[ GeneralTab::TAB_NAME ][ DefaultOption::FIELD_NAME ] = new DefaultOption();

		return $options;
	}
}
