<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use WPDesk\FCF\Free\Field\FieldData;
use WPDesk\FCF\Pro\Field\Type\RadioType;
use WPDesk\FCF\Pro\Field\Type\SelectType;
use WPDesk\FCF\Pro\Field\Type\CheckboxType;
use WPDesk\FCF\Pro\Field\Type\RadioColorsType;
use WPDesk\FCF\Pro\Field\Type\RadioImagesType;
use WPDesk\FCF\Pro\Settings\Option\OptionsOption;
use WPDesk\FCF\Pro\Field\Type\CheckboxDefaultType;
use WPDesk\FCF\Pro\Settings\Option\OptionsKeyOption;
use WPDesk\FCF\Pro\Settings\Option\OptionsValueOption;
use WPDesk\FCF\Free\Collections\RouteParamBag;

/**
 * Options provider for Flexible Checkout Fields plugin fields.
 */
class FlexibleCheckoutFields extends OptionsProvider {

	/**
	 * Fields settings.
	 *
	 * @var array<string, array>
	 */
	private $settings;

	/**
	 * List of supported field types.
	 *
	 * @var array
	 */
	private const SUPPORTED_FIELD_TYPES = [
		CheckboxType::FIELD_TYPE,
		CheckboxDefaultType::FIELD_TYPE,
		SelectType::FIELD_TYPE,
		RadioType::FIELD_TYPE,
		RadioImagesType::FIELD_TYPE,
		RadioColorsType::FIELD_TYPE,
	];

	public function __construct( array $settings ) {
		$this->settings = $settings;
	}

	public function get_label(): string {
		return __( 'FCF Field', 'flexible-checkout-fields-pro' );
	}

	public function get_value(): string {
		return 'fcf_field';
	}

	public function get_selections( RouteParamBag $params ): array {
		$this->update_settings(
			$params->getString( 'form_section' ),
			$params->collection( 'form_fields' )
		);

		$values = [];
		foreach ( $this->settings as $section_fields ) {
			foreach ( $section_fields as $field ) {
				if (
					! isset( $field['name'] ) ||
					! isset( $field['type'] ) ||
					! isset( $field['label'] ) ||
					$field['name'] === $params->getString( 'form_field_name' ) ||
					! in_array( $field['type'] ?? '', self::SUPPORTED_FIELD_TYPES, true )
				) {
					continue;
				}

				$values[ $field['name'] ] = sprintf( '%s (%s)', $field['label'], $field['name'] );
			}
		}
		return $values;
	}

	public function get_comparisons( RouteParamBag $params ): array {
		return [
			'is'     => __( 'Is', 'flexible-checkout-fields-pro' ),
			'is_not' => __( 'Is not', 'flexible-checkout-fields-pro' ),
		];
	}

	public function get_values( RouteParamBag $params ): array {
		$this->update_settings(
			$params->getString( 'form_section' ),
			$params->collection( 'form_fields' )
		);

		$field_name = $params->collection( 'form_values' )->getString( 'selection' );

		foreach ( $this->settings as $section_fields ) {
			if ( ! isset( $section_fields[ $field_name ] ) ) {
				continue;
			}
			return $this->get_values_for_field( $section_fields[ $field_name ] );
		}

		throw new \Exception( 'Field not found: ' . $field_name );
	}

	/**
	 * Get values for different types of field.
	 *
	 * @param array<string, array> $field_data
	 * @return array<string, string>
	 */
	private function get_values_for_field( array $field_data ): array {
		switch ( $field_data['type'] ) {
			case CheckboxType::FIELD_TYPE:
			case CheckboxDefaultType::FIELD_TYPE:
				return [
					'checked'   => __( 'Checked', 'flexible-checkout-fields-pro' ),
					'unchecked' => __( 'Unchecked', 'flexible-checkout-fields-pro' ),
				];
			case SelectType::FIELD_TYPE:
			case RadioType::FIELD_TYPE:
			case RadioImagesType::FIELD_TYPE:
			case RadioColorsType::FIELD_TYPE:
				$field_args = FieldData::get_field_data( $field_data );
				$options    = $field_args[ OptionsOption::FIELD_NAME ] ?? [];
				if ( ! $field_args || ! $options ) {
					return [];
				}
				return array_combine(
					array_column( $options, OptionsKeyOption::FIELD_NAME ),
					array_column( $options, OptionsValueOption::FIELD_NAME )
				);
		}

		return [];
	}


	/**
	 * Returns updated (not yet saved) fields settings using posted data.
	 *
	 * @param string $section_name     Name of section.
	 * @param RouteParamBag  $section_settings Settings of section.
	 */
	protected function update_settings( string $section_name, RouteParamBag $section_settings ): void {
		if ( ! $section_settings ) {
			return;
		}

		$section_fields = $section_settings->reduce(
			function ( $section_fields, $field ) {
				$field_data = FieldData::get_field_data( $field, false );
				if ( ! $field_data ) {
					return $section_fields;
				}
				$section_fields[ $field_data['name'] ] = $field_data;

				return $section_fields;
			},
			[]
		);

		$this->settings[ $section_name ] = $section_fields;
	}
}
