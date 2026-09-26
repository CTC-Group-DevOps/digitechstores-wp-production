<?php

namespace WPDesk\FCF\Pro\Pricing\Field;

/**
 * {@inheritdoc}
 */
class FieldText extends FieldAbstract {

	/**
	 * {@inheritdoc}
	 */
	public function get_options_for_price_values(): array {
		return [
			'_value' => $this->get_field_label(),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field_label_for_fee( $field_value, string $default_value ): string {
		return $this->get_field_label();
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_price_in_field_label( array $args, string $value, string $label ): array {
		$args['label'] .= $label;
		return $args;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_valid_field_value( string $option_value, $current_value ): bool {
		return ( ( $option_value === '_value' ) & ! empty( $current_value ) );
	}
}
