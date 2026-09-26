<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use WPDesk\FCF\Free\Collections\RouteParamBag;

/**
 * Options provider for cart (category) rules.
 */
class Cart extends OptionsProvider {

	public function get_label(): string {
		return __( 'Cart', 'flexible-checkout-fields-pro' );
	}

	public function get_value(): string {
		return 'cart';
	}

	public function get_selections( RouteParamBag $params ): array {
		return [
			'cart_total'       => __( 'Total value', 'flexible-checkout-fields-pro' ),
		];
	}

	public function get_comparisons( RouteParamBag $params ): array {
		return [
			'is'        => __( 'Is', 'flexible-checkout-fields-pro' ),
			'more_than' => __( 'More than', 'flexible-checkout-fields-pro' ),
			'less_than' => __( 'Less than', 'flexible-checkout-fields-pro' ),
		];
	}
}
