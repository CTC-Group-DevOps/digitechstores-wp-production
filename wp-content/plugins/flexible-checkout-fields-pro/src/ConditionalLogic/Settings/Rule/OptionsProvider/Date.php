<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use WPDesk\FCF\Pro\Settings\Option\LogicRuleValues;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleComparison;
use WPDesk\FCF\Free\Collections\RouteParamBag;

/**
 * Options provider for rules around dates.
 */
class Date extends OptionsProvider {

	public function get_label(): string {
		return __( 'Date', 'flexible-checkout-fields-pro' );
	}

	public function get_value(): string {
		return 'date';
	}

	public function rule_definition(): array {
		return [
			LogicRuleComparison::FIELD_NAME,
			LogicRuleValues::FIELD_NAME,
		];
	}

	public function get_comparisons( RouteParamBag $params ): array {
		$items = [
			'from'  => __( 'From', 'flexible-checkout-fields-pro' ),
			'until' => __( 'Until', 'flexible-checkout-fields-pro' ),
		];

		return $items;
	}
}
