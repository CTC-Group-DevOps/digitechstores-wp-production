<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\LogicTab;

/**
 * {@inheritdoc}
 */
class LogicFieldsRulesOption extends OptionAbstract {

	const FIELD_NAME = 'conditional_logic_fields_rules';

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
		return self::FIELD_TYPE_REPEATER;
	}

	/**
	 * Returns label of option row (for Repeater field).
	 *
	 * @return string Option row label.
	 */
	public function get_option_row_label(): string {
		/* translators: %s: rule index */
		return __( 'Rule #%s', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options_regexes_to_display(): array {
		return [
			LogicFieldsEnabledOption::FIELD_NAME => '^1$',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return [
			[
				'field'     => '',
				'condition' => '',
				'value'     => '',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_children(): array {
		return [
			LogicFieldsRulesFieldOption::FIELD_NAME     => new LogicFieldsRulesFieldOption(),
			LogicFieldsRulesConditionOption::FIELD_NAME => new LogicFieldsRulesConditionOption(),
			LogicFieldsRulesValueOption::FIELD_NAME     => new LogicFieldsRulesValueOption(),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitize_option_value( $field_value ) {
		if ( ! $field_value ) {
			return [];
		}

		foreach ( $field_value as $row_index => $row ) {
			if ( ! ( $row['field'] ?? '' )
				|| ! ( $row['condition'] ?? '' )
				|| ( ( $row['value'] ?? '' ) === '' ) ) {
				unset( $field_value[ $row_index ] );
			}
		}
		return $field_value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save_field_data( array $field_data, array $field_settings ): array {
		if ( intval( $field_settings[ LogicFieldsEnabledOption::FIELD_NAME ] ?? 0 ) !== 1 ) {
			return $field_data;
		}

		return $this->update_field_data( $field_data, $field_settings );
	}
}
