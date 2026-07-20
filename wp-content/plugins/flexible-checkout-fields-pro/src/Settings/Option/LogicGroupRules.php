<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Tab\LogicTab;
use WPDesk\FCF\Free\Settings\Option\OptionAbstract;

/**
 * Group of rules option definition.
 */
class LogicGroupRules extends OptionAbstract {

	const FIELD_NAME = 'rules';

	public function get_option_name(): string {
		return self::FIELD_NAME;
	}

	public function get_option_tab(): string {
		return LogicTab::TAB_NAME;
	}

	public function get_option_type(): string {
		return self::FIELD_TYPE_REPEATER_RULES;
	}

	public function get_option_row_label(): string {
		/* translators: %s: rule index */
		return __( 'Rule #%s', 'flexible-checkout-fields-pro' );
	}

	public function get_options_regexes_to_display(): array {
		return [
			LogicGroupAction::FIELD_NAME => '^.{1,}$',
		];
	}

	public function get_default_value() {
		return [
			LogicRuleCategory::FIELD_NAME   => '',
			LogicRuleSelection::FIELD_NAME  => '',
			LogicRuleComparison::FIELD_NAME => '',
			LogicRuleValues::FIELD_NAME     => [],
		];
	}

	public function get_children(): array {
		return [
			LogicRuleCategory::FIELD_NAME   => new LogicRuleCategory(),
			LogicRuleSelection::FIELD_NAME  => new LogicRuleSelection(),
			LogicRuleComparison::FIELD_NAME => new LogicRuleComparison(),
			LogicRuleValues::FIELD_NAME     => new LogicRuleValues(),
		];
	}

	public function is_refresh_trigger(): bool {
		return true;
	}

	/**
	 * Filters field data before it is saved.
	 *
	 * Rules are saved in nested array - inner array and outer array.
	 * Inner arrays represents OR relationship between group of rules.
	 * Outer array represents AND relationship between each rule.
	 *
	 * @param array $field_data
	 * @param array $field_settings
	 *
	 * @return array
	 */
	public function update_field_data( array $field_data, array $field_settings ): array {
		$option_name = $this->get_option_name();

		if ( ! isset( $field_settings[ $option_name ] ) || empty( $field_settings[ $option_name ] ) ) {
			return [];
		}

		foreach ( $field_settings[ $option_name ] as $or_index => $and_group ) {
			foreach ( $and_group as $and_index => $rule ) {
				foreach ( $this->get_children() as $option_children ) {
					$field_data[ $option_name ][ $or_index ][ $and_index ] = $option_children->update_field_data(
						$field_data[ $option_name ][ $or_index ][ $and_index ] ?? [],
						$rule
					);
				}
			}
		}

		return $field_data;
	}
}
