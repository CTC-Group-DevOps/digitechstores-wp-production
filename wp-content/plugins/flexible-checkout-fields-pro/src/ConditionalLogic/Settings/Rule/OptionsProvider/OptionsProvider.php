<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use WPDesk\FCF\Free\Collections\RouteParamBag;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleValues;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleSelection;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleComparison;

/**
 * Base class for conditional logic options providers.
 * Every rule category must implement this class.
 */
abstract class OptionsProvider {

	/**
	 * Rule category name
	 *
	 * @return string
	 */
	abstract public function get_label(): string;

	/**
	 * Rule category value
	 *
	 * @return string
	 */
	abstract public function get_value(): string;

	/**
	 * Rule fields (steps) order and display definition.
	 *
	 * @return array<string>
	 */
	public function rule_definition(): array {
		return [
			LogicRuleSelection::FIELD_NAME,
			LogicRuleComparison::FIELD_NAME,
			LogicRuleValues::FIELD_NAME,
		];
	}

	/**
	 * Retrieves the selection options based on the given parameters.
	 * Selection field i used to be more specific about the rule.
	 *
	 * @param RouteParamBag<string, mixed> $params Parameters with current form values.
	 * @return array<string, string> The array of selections.
	 */
	public function get_selections( RouteParamBag $params ): array {
		return [];
	}

	/**
	 * Retrieves the comparisons options based on the given parameters.
	 * Comparison field defines how to compare the rule values.
	 *
	 * @param RouteParamBag<string, mixed> $params Parameters with current form values.
	 * @return array<string, string> The array of comparisons.
	 */
	public function get_comparisons( RouteParamBag $params ): array {
		return [];
	}

	/**
	 * Retrieves the values options based on the given parameters.
	 *
	 * @param RouteParamBag<string, mixed> $params Parameters with current form values.
	 * @return array<string, string> The array of values.
	 */
	public function get_values( RouteParamBag $params ): array {
		return [];
	}
}
