<?php

namespace WPDesk\FCF\Pro\Pricing\Field;

use WPDesk\FCF\Pro\Settings\Option\OptionsKeyOption;
use WPDesk\FCF\Pro\Settings\Option\OptionsOption;
use WPDesk\FCF\Pro\Settings\Option\OptionsValueOption;

/**
 * {@inheritdoc}
 */
abstract class FieldAbstract implements FieldInterface {

	const OPTION_PRICING_ENABLED = 'pricing_enabled';
	const OPTION_PRICING_VALUES  = 'pricing_values';

	/**
	 * Settings of field.
	 *
	 * @var array
	 */
	private $field_data = [];

	/**
	 * Class constructor.
	 *
	 * @param array $field_data Settings of field.
	 */
	public function __construct( array $field_data ) {
		$this->field_data = $field_data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_pricing_enabled(): bool {
		return ( isset( $this->field_data[ self::OPTION_PRICING_ENABLED ] ) && $this->field_data[ self::OPTION_PRICING_ENABLED ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field_name(): string {
		return $this->field_data['name'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field_label(): string {
		return $this->field_data['label'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field_options(): array {
		$options = [];
		foreach ( $this->field_data[ OptionsOption::FIELD_NAME ] as $option_data ) {
			$options[ $option_data[ OptionsKeyOption::FIELD_NAME ] ] = $option_data[ OptionsValueOption::FIELD_NAME ];
		}

		return $options;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field_pricing_types(): array {
		$types = $this->field_data[ self::OPTION_PRICING_VALUES ] ?? [];
		foreach ( $types as $value => $type ) {
			if ( ! is_numeric( $type['value'] ) ) {
				unset( $types[ $value ] );
			}
		}
		return $types;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field_label_for_settings(): string {
		/* translators: %s: field label */
		return __( 'Pricing options for %s', 'flexible-checkout-fields-pro' );
	}
}
