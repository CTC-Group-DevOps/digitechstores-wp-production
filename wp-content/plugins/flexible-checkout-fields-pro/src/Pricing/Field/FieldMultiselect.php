<?php

namespace WPDesk\FCF\Pro\Pricing\Field;

/**
 * {@inheritdoc}
 */
class FieldMultiselect extends FieldRadio {

	/**
	 * {@inheritdoc}
	 */
	public function get_field_label_for_fee( $field_value, string $default_value ): string {
		$options = $this->get_field_options();
		return sprintf( '%s: %s', $this->get_field_label(), $options[ $default_value ] ?? $field_value );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_valid_field_value( string $option_value, $current_value ): bool {
		if ( ! is_array( $current_value ) ) {
			return false;
		}
		return ( in_array( $option_value, $current_value, true ) );
	}
}
