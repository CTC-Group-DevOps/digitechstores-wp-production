<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use WPDesk\FCF\Free\Collections\RouteParamBag;

/**
 * Options provider for Woo checkout fields.
 */
class WooFields extends OptionsProvider {

	/**
	 * List of unsupported field types.
	 *
	 * @var array
	 */
	private const EXCLUDED_FIELD_NAMES = [];

	public function get_label(): string {
		return __( 'Woo fields', 'flexible-checkout-fields-pro' );
	}

	public function get_value(): string {
		return 'woo_field';
	}

	public function get_selections( RouteParamBag $param ): array {
		$form_field_name = $param->getString( 'form_field_name' );

		$settings = WC()->checkout->get_checkout_fields(); // @phpstan-ignore property.notFound
		$values   = [];
		foreach ( $settings as $section_name => $section_fields ) {
			foreach ( $section_fields as $field_name => $field ) {
				if ( $field_name === $form_field_name ) {
					continue;
				}

				if ( in_array( $field_name, self::EXCLUDED_FIELD_NAMES, true ) ) {
					continue;
				}

				if ( isset( $field['custom_field'] ) && $field['custom_field'] ) {
					continue;
				}

				$values[ $field_name ] = sprintf( '%s: %s', ucfirst( $section_name ), $field['label'] );
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
		$field_name   = $params->collection( 'form_values' )->getString( 'selection' );
		$field_search = $params->getString( 'field_search' );

		switch ( $field_name ) {
			case 'billing_country':
			case 'shipping_country':
				$countries         = new \WC_Countries();
				$list_of_countries = $countries->__get( 'countries' );
				break;
			default:
				$list_of_countries = [];
				break;
		}

		if ( ! $field_search ) {
			return $list_of_countries;
		}

		return array_filter(
			$list_of_countries,
			function ( $country ) use ( $field_search ) {
				return stripos( $country, $field_search ) !== false;
			}
		);
	}
}
