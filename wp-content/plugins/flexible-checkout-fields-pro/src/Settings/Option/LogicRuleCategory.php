<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Tab\LogicTab;
use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Route\RuleCategoryRoute;

/**
 * Rule category option definition.
 */
class LogicRuleCategory extends OptionAbstract {

	const FIELD_NAME = 'category';

	public function get_option_name(): string {
		return self::FIELD_NAME;
	}

	public function get_option_tab(): string {
		return LogicTab::TAB_NAME;
	}

	public function get_option_type(): string {
		return self::FIELD_TYPE_SELECT;
	}

	public function get_endpoint_route(): string {
		return RuleCategoryRoute::REST_API_ROUTE;
	}

	public function get_validation_rules(): array {
		return [
			'^.{1,}$' => __( 'This field is required.', 'flexible-checkout-fields-pro' ),
		];
	}

	public function is_refresh_trigger(): bool {
		return true;
	}
}
