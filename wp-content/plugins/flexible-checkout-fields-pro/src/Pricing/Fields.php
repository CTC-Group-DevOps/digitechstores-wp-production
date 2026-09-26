<?php

namespace WPDesk\FCF\Pro\Pricing;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FCF\Pro\Field\Type\CheckboxType;
use WPDesk\FCF\Pro\Field\Type\ColorType;
use WPDesk\FCF\Pro\Field\Type\DateType;
use WPDesk\FCF\Pro\Field\Type\EmailType;
use WPDesk\FCF\Pro\Field\Type\FileType;
use WPDesk\FCF\Pro\Field\Type\MultiCheckboxType;
use WPDesk\FCF\Pro\Field\Type\MultiSelectType;
use WPDesk\FCF\Pro\Field\Type\NumberType;
use WPDesk\FCF\Pro\Field\Type\PhoneType;
use WPDesk\FCF\Pro\Field\Type\RadioColorsType;
use WPDesk\FCF\Pro\Field\Type\RadioImagesType;
use WPDesk\FCF\Pro\Field\Type\RadioType;
use WPDesk\FCF\Pro\Field\Type\SelectType;
use WPDesk\FCF\Pro\Field\Type\TextareaType;
use WPDesk\FCF\Pro\Field\Type\TextType;
use WPDesk\FCF\Pro\Field\Type\TimeType;
use WPDesk\FCF\Pro\Field\Type\UrlType;
use WPDesk\FCF\Pro\Plugin\Settings as PluginSettings;
use WPDesk\FCF\Pro\Pricing\Field\FieldIntegration;
use WPDesk\FCF\Pro\Pricing\Field\FieldInterface;
use WPDesk\FCF\Pro\Pricing\Field\FieldMultiselect;
use WPDesk\FCF\Pro\Pricing\Field\FieldRadio;
use WPDesk\FCF\Pro\Pricing\Field\FieldText;

/**
 * Support fields types for Pricing.
 */
class Fields implements Hookable {

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'init_pricing_for_fields' ] );
	}

	/**
	 * Initiates loading of pricing handling for fields with enabled option.
	 *
	 * @internal
	 */
	public function init_pricing_for_fields() {
		$sections = ( new PluginSettings() )->get_settings_for_fields();
		if ( ! $sections || ! is_array( $sections ) ) {
			return;
		}

		foreach ( $sections as $fields ) {
			if ( ! $fields ) {
				continue;
			}
			foreach ( $fields as $field_key => $field ) {
				if ( isset( $field['custom_field'] ) && $field['custom_field'] ) {
					$this->init_pricing_for_field( $field );
				}
			}
		}
	}

	/**
	 * Initiates loading of pricing handling for field.
	 *
	 * @param array $field Settings of field.
	 */
	private function init_pricing_for_field( array $field ) {
		$field_object = $this->create_field( $field );
		if ( $field_object === null ) {
			return;
		}

		( new FieldIntegration( $field_object ) )->hooks();
		( new Types( $field_object ) )->load_pricing_types();
	}

	/**
	 * Returns class object for handling of field type.
	 *
	 * @param array $field Settings of field.
	 *
	 * @return FieldInterface|null Class object of field type, if is available.
	 */
	private function create_field( array $field ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		switch ( $field['type'] ?? 'text' ) {
			case TextType::FIELD_TYPE:
			case TextareaType::FIELD_TYPE:
			case NumberType::FIELD_TYPE:
			case EmailType::FIELD_TYPE:
			case PhoneType::FIELD_TYPE:
			case UrlType::FIELD_TYPE:
			case CheckboxType::FIELD_TYPE:
			case ColorType::FIELD_TYPE:
			case DateType::FIELD_TYPE:
			case TimeType::FIELD_TYPE:
			case FileType::FIELD_TYPE:
				return new FieldText( $field );
			case SelectType::FIELD_TYPE:
			case RadioType::FIELD_TYPE:
			case RadioImagesType::FIELD_TYPE:
			case RadioColorsType::FIELD_TYPE:
				return new FieldRadio( $field );
			case MultiCheckboxType::FIELD_TYPE:
			case MultiSelectType::FIELD_TYPE:
				return new FieldMultiselect( $field );
		}
		return null;
	}
}
