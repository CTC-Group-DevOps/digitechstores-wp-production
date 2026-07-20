<?php

namespace WPDesk\FCF\Pro\Settings\Option;

use WPDesk\FCF\Free\Settings\Option\OptionAbstract;
use WPDesk\FCF\Free\Settings\Tab\AdvancedTab;

/**
 * {@inheritdoc}
 */
class FileTypesOption extends OptionAbstract {

	const FIELD_NAME = 'file_types';

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
		return AdvancedTab::TAB_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_type(): string {
		return self::FIELD_TYPE_SELECT_MULTI;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option_label(): string {
		return __( 'Allowed file types', 'flexible-checkout-fields-pro' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_validation_rules(): array {
		return [
			'^.{1,}$' => __( 'This field is required.', 'flexible-checkout-fields-pro' ),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_values(): array {
		$mime_types = get_allowed_mime_types();
		$values     = [];
		foreach ( $mime_types as $extensions => $mime_type ) {
			$values[ $mime_type ] = sprintf(
				'.%1$s (%2$s)',
				implode( ', .', explode( '|', $extensions ) ),
				$mime_type
			);
		}

		return $values;
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_field_data( array $field_data, array $field_settings ): array {
		$option_name = $this->get_option_name();
		if ( $option_name === '' ) {
			return $field_data;
		}

		$values = $field_settings[ $option_name ] ?? $this->get_default_value();
		if ( ! is_array( $values ) ) {
			$values = explode( ',', $values );
		}

		$field_data[ $option_name ] = array_filter( $values );
		return $field_data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save_field_data( array $field_data, array $field_settings ): array {
		$option_name = $this->get_option_name();
		$values      = $field_settings[ $option_name ] ?? $this->get_default_value();

		$field_data[ $option_name ] = ( $values ) ? implode( ',', $values ) : '';
		return $field_data;
	}
}
