<?php

namespace WPDesk\FCF\Pro\Field\Type;

use WPDesk\FCF\Free\Field\Type\HiddenType as DefaultHiddenType;
use WPDesk\FCF\Free\Settings\Option\CustomFieldOption;
use WPDesk\FCF\Free\Settings\Option\DisplayOnOption;
use WPDesk\FCF\Free\Settings\Option\EnabledOption;
use WPDesk\FCF\Free\Settings\Option\ExternalFieldInfoOption;
use WPDesk\FCF\Free\Settings\Option\ExternalFieldOption;
use WPDesk\FCF\Free\Settings\Option\FieldTypeOption;
use WPDesk\FCF\Free\Settings\Option\LabelOption;
use WPDesk\FCF\Free\Settings\Option\NameOption;
use WPDesk\FCF\Free\Settings\Option\PriorityOption;
use WPDesk\FCF\Free\Settings\Tab\DisplayTab;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;
use WPDesk\FCF\Pro\Settings\Option\DisplayOnEmptyOption;
use WPDesk\FCF\Pro\Settings\Option\HiddenFieldInfoOption;

/**
 * {@inheritdoc}
 */
class HiddenType extends DefaultHiddenType {

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
			GeneralTab::TAB_NAME => [
				ExternalFieldInfoOption::FIELD_NAME => new ExternalFieldInfoOption(),
				PriorityOption::FIELD_NAME          => new PriorityOption(),
				FieldTypeOption::FIELD_NAME         => new FieldTypeOption(),
				EnabledOption::FIELD_NAME           => new EnabledOption(),
				CustomFieldOption::FIELD_NAME       => new CustomFieldOption(),
				ExternalFieldOption::FIELD_NAME     => new ExternalFieldOption(),
				LabelOption::FIELD_NAME             => new LabelOption(),
				NameOption::FIELD_NAME              => new NameOption(),
				HiddenFieldInfoOption::FIELD_NAME   => new HiddenFieldInfoOption(),
			],
			DisplayTab::TAB_NAME => [
				DisplayOnEmptyOption::FIELD_NAME => new DisplayOnEmptyOption(),
			],
		];
	}
}
