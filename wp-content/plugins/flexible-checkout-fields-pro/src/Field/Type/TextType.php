<?php

namespace WPDesk\FCF\Pro\Field\Type;

use WPDesk\FCF\Free\Settings\Tab\LogicTab;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;
use WPDesk\FCF\Free\Settings\Tab\PricingTab;
use WPDesk\FCF\Pro\Settings\Option\LogicGroup;
use WPDesk\FCF\Pro\Settings\Option\DefaultOption;
use WPDesk\FCF\Pro\Settings\Option\LogicInfoOption;
use WPDesk\FCF\Pro\Settings\Option\PricingInfoOption;
use WPDesk\FCF\Pro\Settings\Option\PricingValueOption;
use WPDesk\FCF\Free\Field\Type\TextType as DefaultTextType;
use WPDesk\FCF\Pro\Settings\Option\PricingEnabledOption;
use WPDesk\FCF\Pro\Settings\Option\LogicFieldsGroupOption;
use WPDesk\FCF\Pro\Settings\Option\LogicFieldsRulesOption;
use WPDesk\FCF\Pro\Settings\Option\LogicFieldsEnabledOption;
use WPDesk\FCF\Pro\Settings\Option\LogicProductsGroupOption;
use WPDesk\FCF\Pro\Settings\Option\LogicProductsRulesOption;
use WPDesk\FCF\Pro\Settings\Option\LogicShippingGroupOption;
use WPDesk\FCF\Pro\Settings\Option\LogicShippingRulesOption;
use WPDesk\FCF\Pro\Settings\Option\LogicProductsEnabledOption;
use WPDesk\FCF\Pro\Settings\Option\LogicShippingEnabledOption;

/**
 * {@inheritdoc}
 */
class TextType extends DefaultTextType {

	/**
	 * {@inheritdoc}
	 */
	public function get_options_objects(): array {
		$options = array_merge(
			parent::get_options_objects(),
			[
				LogicTab::TAB_NAME => [
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
