<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\GeneralTab;

/**
 * {@inheritdoc}
 */
class OptionsOption extends OptionAbstract {

	const FIELD_NAME = 'options';

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
		return GeneralTab::TAB_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_REPEATER;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		return __( 'Options', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label_tooltip(): string {
		return __( 'Enter a value and a label for each field option. The value will not be visible in the form. The label will be visible.', 'flexible-checkout-fields-pro' );
	}

	/**
	 * Returns label of option row (for Repeater field).
	 *
	 * @return string Option row label.
	 */
	public function get_option_row_label(): string {
		/* translators: %s row index */
		return __( 'Option #%s', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value() {
		return [
			[
				OptionsKeyOption::FIELD_NAME   => '',
				OptionsValueOption::FIELD_NAME => '',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_refresh_trigger(): bool {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_children(): array {
		return [
			OptionsKeyOption::FIELD_NAME   => new OptionsKeyOption(),
			OptionsValueOption::FIELD_NAME => new OptionsValueOption(),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_field_data( array $field_data, array $field_settings ): array {
		$option_name = $this->get_option_name();

		$values     = $field_settings[ $option_name ] ?? $this->get_default_value();
		$new_values = [];
		foreach ( $values as $option_key => $option_value ) {
			if ( is_array( $option_value ) ) {
				$new_values[] = $option_value;
			} else {
				$new_values[] = [
					OptionsKeyOption::FIELD_NAME   => $option_key,
					OptionsValueOption::FIELD_NAME => $option_value,
				];
			}
		}

		$field_data[ $option_name ] = $this->sanitize_option_value( $new_values );
		return $field_data;
	}
}
