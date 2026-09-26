<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\LogicTab;
use WPDesk\FCF\Pro\Settings\Route\FieldsValueRoute;

/**
 * {@inheritdoc}
 */
class LogicFieldsRulesValueOption extends OptionAbstract {

	const FIELD_NAME = 'value';

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
		return __( 'Value', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_validation_rules(): array {
		return [
			'^.{1,}$' => __( 'This field is required.', 'flexible-checkout-fields-pro' ),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options_regexes_to_display(): array {
		return [
			LogicFieldsRulesFieldOption::FIELD_NAME     => '^.{1,}$',
			LogicFieldsRulesConditionOption::FIELD_NAME => '^.{1,}$',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_endpoint_route(): string {
		return FieldsValueRoute::REST_API_ROUTE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_endpoint_option_names(): array {
		return [
			LogicFieldsRulesFieldOption::FIELD_NAME,
			LogicFieldsRulesConditionOption::FIELD_NAME,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_endpoint_autorefreshed(): bool {
		return true;
	}
}
