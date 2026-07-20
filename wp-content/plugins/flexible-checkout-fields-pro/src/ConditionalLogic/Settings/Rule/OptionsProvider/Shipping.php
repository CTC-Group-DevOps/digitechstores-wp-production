<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use WPDesk\FCF\Free\Collections\RouteParamBag;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleComparison;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleValues;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleSelection;


/**
 * Options provider for rules around shipping methods.
 */
class Shipping extends OptionsProvider {

	/**
	 * Supported shipping.
	 *
	 * @var array
	 */
	private const SUPPORTED_SHIPPING_METHODS = [
		'paczkomaty_shipping_method',
		'polecony_paczkomaty_shipping_method',
		'enadawca',
		'paczka_w_ruchu',
		'dhl',
		'dpd',
		'furgonetka',
		'flexible_shipping_info',
	];

	private const ZONE_DEFAULT_KEY = 'no_shipping_zones';

	public function get_label(): string {
		return __( 'Shipping method', 'flexible-checkout-fields-pro' );
	}

	public function get_value(): string {
		return 'shipping_method';
	}

	public function rule_definition(): array {
		return [
			LogicRuleSelection::FIELD_NAME,
			LogicRuleComparison::FIELD_NAME,
			LogicRuleValues::FIELD_NAME,
		];
	}

	public function get_selections( RouteParamBag $params ): array {
		$zones = \WC_Shipping_Zones::get_zones();

		$values = [
			self::ZONE_DEFAULT_KEY => __( 'No Shipping Zones or Global Methods', 'flexible-checkout-fields-pro' ),
		];
		foreach ( $zones as $zone ) {
			$values[ $zone['zone_id'] ] = $zone['zone_name'];
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
		$selection = $params->collection( 'form_values' )->getString( 'selection' );

		if ( $selection !== self::ZONE_DEFAULT_KEY ) {
			return $this->get_shipping_methods_by_zone( (int) $selection );
		} else {
			return array_merge(
				$this->get_shipping_methods_by_zone( 0 ),
				$this->get_shipping_methods_for_global()
			);
		}
	}

	/**
	 * Returns shipping methods for shipping zone.
	 *
	 * @param int $zone_id ID of shipping zone.
	 *
	 * @return \WC_Shipping_Method[] List of shipping methods.
	 */
	private function get_shipping_methods_by_zone( int $zone_id ): array {
		$zone    = \WC_Shipping_Zones::get_zone( $zone_id );
		$methods = [];
		foreach ( $zone->get_shipping_methods( true ) as $shipping_method ) {
			if ( ! $this->is_supported_shipping_method( $shipping_method ) ) {
				continue;
			}
			$methods = array_merge( $methods, $this->get_shipping_methods( $shipping_method ) );
		}
		return $methods;
	}

	/**
	 * Returns shipping methods used globally.
	 *
	 * @return \WC_Shipping_Method[] List of shipping methods.
	 */
	private function get_shipping_methods_for_global(): array {
		$shipping_methods = \WC()->shipping->load_shipping_methods();
		$methods          = [];
		foreach ( $shipping_methods as $shipping_method ) {
			if ( $this->is_supported_shipping_method( $shipping_method )
				|| ! isset( $shipping_method->id )
				|| in_array( $shipping_method->id, self::SUPPORTED_SHIPPING_METHODS, true ) ) {
				continue;
			}
			$methods[ $shipping_method->id ] = $shipping_method->method_title;
		}

		return $methods;
	}

	/**
	 * Returns status of whether Shipping Method is supported.
	 *
	 * @param \WC_Shipping_Method $shipping_method WooCommerce Shipping Method Class.
	 *
	 * @return bool Status of supported.
	 */
	private function is_supported_shipping_method( \WC_Shipping_Method $shipping_method ): bool {
		foreach ( $shipping_method->supports as $support ) {
			if ( in_array( $support, [ 'flexible-shipping', 'shipping-zones' ], true ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns list of shipping method options.
	 *
	 * @param \WC_Shipping_Method $shipping_method WooCommerce Shipping Method Class.
	 *
	 * @return array List of shipping methods.
	 */
	private function get_shipping_methods( \WC_Shipping_Method $shipping_method ): array {
		$methods = [];
		switch ( $shipping_method->id ) {
			case 'flexible_shipping':
				$option_name = $shipping_method->shipping_methods_option ?? null;
				if ( ! $option_name ) {
					break;
				}

				$fs_methods = get_option( $option_name, [] );
				foreach ( $fs_methods as $fs_method ) {
					$methods[ $fs_method['id_for_shipping'] ] = sprintf( 'Flexible Shipping: %s', $fs_method['method_title'] );
				}
				break;
			default:
				$key             = sprintf( '%s:%s', $shipping_method->id, $shipping_method->instance_id );
				$methods[ $key ] = $shipping_method->title;
				break;
		}
		return $methods;
	}
}
