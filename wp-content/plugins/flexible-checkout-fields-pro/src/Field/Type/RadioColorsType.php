<?php

namespace WPDesk\FCF\Pro\Field\Type;

use WPDesk\FCF\Free\Settings\Tab\LogicTab;
use WPDesk\FCF\Free\Settings\Tab\DisplayTab;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;
use WPDesk\FCF\Free\Settings\Tab\PricingTab;
use WPDesk\FCF\Free\Settings\Tab\AdvancedTab;
use WPDesk\FCF\Free\Settings\Option\CssOption;
use WPDesk\FCF\Pro\Settings\Option\LogicGroup;
use WPDesk\FCF\Free\Settings\Option\NameOption;
use WPDesk\FCF\Free\Settings\Tab\AppearanceTab;
use WPDesk\FCF\Free\Settings\Option\LabelOption;
use WPDesk\FCF\Free\Settings\Option\EnabledOption;
use WPDesk\FCF\Free\Settings\Option\PriorityOption;
use WPDesk\FCF\Free\Settings\Option\RequiredOption;
use WPDesk\FCF\Pro\Settings\Option\LogicInfoOption;
use WPDesk\FCF\Free\Settings\Option\DisplayOnOption;
use WPDesk\FCF\Free\Settings\Option\FieldTypeOption;
use WPDesk\FCF\Free\Settings\Option\FormattingOption;
use WPDesk\FCF\Free\Settings\Option\ValidationOption;
use WPDesk\FCF\Pro\Settings\Option\PricingInfoOption;
use WPDesk\FCF\Free\Settings\Option\CustomFieldOption;
use WPDesk\FCF\Pro\Settings\Option\PreviewLabelOption;
use WPDesk\FCF\Pro\Settings\Option\PreviewWidthOption;
use WPDesk\FCF\Pro\Settings\Option\PricingValuesOption;
use WPDesk\FCF\Pro\Settings\Option\DefaultOptionsOption;
use WPDesk\FCF\Pro\Settings\Option\PricingEnabledOption;
use WPDesk\FCF\Free\Settings\Option\ValidationInfoOption;
use WPDesk\FCF\Pro\Settings\Option\LogicFieldsGroupOption;
use WPDesk\FCF\Pro\Settings\Option\LogicFieldsRulesOption;
use WPDesk\FCF\Pro\Settings\Option\OptionsWithColorOption;
use WPDesk\FCF\Pro\Settings\Option\LogicFieldsEnabledOption;
use WPDesk\FCF\Pro\Settings\Option\LogicProductsGroupOption;
use WPDesk\FCF\Pro\Settings\Option\LogicProductsRulesOption;
use WPDesk\FCF\Pro\Settings\Option\LogicShippingGroupOption;
use WPDesk\FCF\Pro\Settings\Option\LogicShippingRulesOption;
use WPDesk\FCF\Pro\Settings\Option\LogicProductsEnabledOption;
use WPDesk\FCF\Pro\Settings\Option\LogicShippingEnabledOption;
use WPDesk\FCF\Free\Field\Type\RadioColorsType as DefaultRadioColorsType;

/**
 * {@inheritdoc}
 */
class RadioColorsType extends DefaultRadioColorsType {

	/**
	 * {@inheritdoc}
	 */
	public function is_available(): bool {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options_objects(): array {
		return [
			GeneralTab::TAB_NAME    => [
				PriorityOption::FIELD_NAME         => new PriorityOption(),
				FieldTypeOption::FIELD_NAME        => new FieldTypeOption(),
				CustomFieldOption::FIELD_NAME      => new CustomFieldOption(),
				EnabledOption::FIELD_NAME          => new EnabledOption(),
				RequiredOption::FIELD_NAME         => new RequiredOption(),
				LabelOption::FIELD_NAME            => new LabelOption(),
				OptionsWithColorOption::FIELD_NAME => new OptionsWithColorOption(),
				DefaultOptionsOption::FIELD_NAME   => new DefaultOptionsOption(),
				NameOption::FIELD_NAME             => new NameOption(),
			],
			AdvancedTab::TAB_NAME   => [
				PreviewWidthOption::FIELD_NAME   => new PreviewWidthOption(),
				PreviewLabelOption::FIELD_NAME   => new PreviewLabelOption(),
				ValidationOption::FIELD_NAME     => new ValidationOption(),
				ValidationInfoOption::FIELD_NAME => new ValidationInfoOption(),
			],
			AppearanceTab::TAB_NAME => [
				CssOption::FIELD_NAME => new CssOption(),
			],
			DisplayTab::TAB_NAME    => [
				DisplayOnOption::FIELD_NAME  => new DisplayOnOption(),
				FormattingOption::FIELD_NAME => new FormattingOption(),
			],
			LogicTab::TAB_NAME => [
				LogicGroup::FIELD_NAME => new LogicGroup(),
			],
			PricingTab::TAB_NAME    => [
				PricingEnabledOption::FIELD_NAME => new PricingEnabledOption(),
				PricingValuesOption::FIELD_NAME  => new PricingValuesOption(),
				PricingInfoOption::FIELD_NAME    => new PricingInfoOption(),
			],
		];
	}
}
