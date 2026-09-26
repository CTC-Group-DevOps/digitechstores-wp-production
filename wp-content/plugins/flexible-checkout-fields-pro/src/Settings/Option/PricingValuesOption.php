<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\PricingTab;

/**
 * {@inheritdoc}
 */
class PricingValuesOption extends OptionAbstract {

	const FIELD_NAME = 'pricing_values';

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
		return PricingTab::TAB_NAME;
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
		/* translators: %s: option value */
		return __( 'Pricing options for "%s"', 'flexible-checkout-fields-pro' );
	}

	/**
	 * Returns name of option whose value will create list of rows for Repeater field.
	 *
	 * @return string Option name.
	 */
	public function get_option_name_to_rows(): string {
		return OptionsOption::FIELD_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return [
			'_value' => [
				'type'      => '',
				'value'     => 0,
				'tax_class' => '',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options_regexes_to_display(): array {
		return [
			PricingEnabledOption::FIELD_NAME => '^1$',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_children(): array {
		return [
			PricingValuesTypeOption::FIELD_NAME  => new PricingValuesTypeOption(),
			PricingValuesValueOption::FIELD_NAME => new PricingValuesValueOption(),
			PricingValuesTaxesOption::FIELD_NAME => new PricingValuesTaxesOption(),
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
			$type  = $row['type'] ?? '';
			$value = $row['value'] ?? '';

			if ( '' === $type || ! is_numeric( $value ) ) {
				unset( $field_value[ $row_index ] );
			}
		}
		return $field_value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save_field_data( array $field_data, array $field_settings ): array {
		if ( intval( $field_settings[ PricingEnabledOption::FIELD_NAME ] ?? 0 ) !== 1 ) {
			return $field_data;
		}

		$field_data     = $this->update_field_data( $field_data, $field_settings );
		$option_name    = $this->get_option_name();
		$options_values = $field_data[ $option_name ] ?? [];
		if ( ! $options_values ) {
			return $field_data;
		}

		$options      = array_filter( $field_settings[ OptionsOption::FIELD_NAME ] ?? [] );
		$options_keys = array_column( $options, 'key' );

		foreach ( $options_values as $option_index => $option ) {
			if ( ( $option_index === '' ) || ! in_array( $option_index, $options_keys, false ) ) {
				unset( $options_values[ $option_index ] );
			}
		}

		$field_data[ $option_name ] = $options_values;
		return $field_data;
	}
}
