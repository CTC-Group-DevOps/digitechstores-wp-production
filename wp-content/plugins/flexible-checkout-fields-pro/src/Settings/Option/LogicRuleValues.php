<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Tab\LogicTab;
use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Route\RuleValuesRoute;

/**
 * Rule values option definition.
 */
class LogicRuleValues extends OptionAbstract {

	const FIELD_NAME = 'values';

	public function get_option_name(): string {
		return self::FIELD_NAME;
	}

	public function get_option_tab(): string {
		return LogicTab::TAB_NAME;
	}

	public function get_option_type(): string {
		return self::FIELD_TYPE_DISPATCHER;
	}

	public function get_endpoint_route(): string {
		return RuleValuesRoute::REST_API_ROUTE;
	}

	public function get_endpoint_option_names(): array {
		return [
			LogicRuleCategory::FIELD_NAME,
			LogicRuleSelection::FIELD_NAME,
			LogicRuleComparison::FIELD_NAME,
		];
	}

	public function get_validation_rules(): array {
		return [
			'^.{1,}$' => __( 'This field is required.', 'flexible-checkout-fields-pro' ),
		];
	}

	/**
	 * Filters field data before it is saved.
	 *
	 * @param array<string, mixed> $field_data Field data.
	 * @param array<string, mixed> $rule_values Rule values.
	 *
	 * @return array<string, mixed> Updated field data.
	 */
	public function update_field_data( array $field_data, array $rule_values ): array {
		$option_name = $this->get_option_name();

		if ( ! isset( $rule_values[ $option_name ] ) ) {
			return [];
		}

		if ( ! is_array( $rule_values[ $option_name ] ) ) {
			$field_data[ $option_name ] = \sanitize_text_field( \wp_unslash( $rule_values[ $option_name ] ) );
			return $field_data;
		}

		foreach ( $rule_values[ $option_name ] as $value ) {
			$field_data[ $option_name ][] = \sanitize_text_field( \wp_unslash( $value ) );
		}

		return $field_data;
	}
}
