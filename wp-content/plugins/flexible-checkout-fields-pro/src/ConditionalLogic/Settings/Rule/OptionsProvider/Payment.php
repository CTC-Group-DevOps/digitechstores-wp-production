<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use WPDesk\FCF\Free\Collections\RouteParamBag;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleComparison;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleSelection;
use WPDesk\FCF\Pro\Settings\Option\LogicRuleValues;

/**
 * Options provider for rules around payment methods.
 */
class Payment extends OptionsProvider {


	public function get_label(): string {
		return __( 'Payment method', 'flexible-checkout-fields-pro' );
	}

	public function get_value(): string {
		return 'payment_method';
	}

	public function rule_definition(): array {
		return [
			LogicRuleComparison::FIELD_NAME,
			LogicRuleValues::FIELD_NAME,
		];
	}

	public function get_comparisons( RouteParamBag $params ): array {
		return [
			'is'     => __( 'Is', 'flexible-checkout-fields-pro' ),
			'is_not' => __( 'Is not', 'flexible-checkout-fields-pro' ),
		];
	}

	public function get_values( RouteParamBag $params ): array {
		$gateways = \WC()->payment_gateways->payment_gateways();

		return array_reduce(
			$gateways,
			function ( $values, $gateway ) {
				$values[ $gateway->id ] = $gateway->get_method_title();
				return $values;
			},
			[]
		);
	}
}
