<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Resolvers;

/**
 * Rule resolver for shipping rules.
 */
class ShippingRuleResolver implements RuleResolver {
	use ResultComparisonAware;

	public function can_resolve(): bool {
		return true;
	}

	public function resolve( array $rules ): bool {
		$session                 = \WC()->session;
		$chosen_shipping_methods = $session->get( 'chosen_shipping_methods' );

		$raw_result = false;
		if ( ! empty( $chosen_shipping_methods ) ) {
			$raw_result = $this->has_same_values( $rules['values'], $chosen_shipping_methods );
		}

		return $this->get_result_by_comparison( $raw_result, $rules['comparison'] );
	}
}
