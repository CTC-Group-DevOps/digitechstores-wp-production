<?php

namespace WPDesk\FCF\Pro\Field\Type;

use WPDesk\FCF\Free\Settings\Option\FieldTypeOption;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;
use WPDesk\FCF\Pro\Field\Type\DefaultType;

/**
 * {@inheritdoc}
 */
class RadioDefaultType extends DefaultType {

	const FIELD_TYPE = 'radio';

	/**
	 * {@inheritdoc}
	 */
	public function get_field_type(): string {
		return self::FIELD_TYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options_objects(): array {
		$options = parent::get_options_objects();

		$options[ GeneralTab::TAB_NAME ][ FieldTypeOption::FIELD_NAME ] = new FieldTypeOption();

		return $options;
	}
}
