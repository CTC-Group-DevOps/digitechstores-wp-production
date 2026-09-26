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
			$raw_result = $this->matches_shipping_methods( $rules['values'], $chosen_shipping_methods );
		}

		return $this->get_result_by_comparison( $raw_result, $rules['comparison'] );
	}

	/**
	 * Check if any chosen shipping method starts with a configured value.
	 *
	 * @param array<int, string> $values Configured shipping methods.
	 * @param array<int, string> $chosen_shipping_methods Chosen shipping methods.
	 */
	private function matches_shipping_methods( array $values, array $chosen_shipping_methods ): bool {
		foreach ( $chosen_shipping_methods as $chosen_shipping_method ) {
			foreach ( $values as $value ) {
				if ( $value !== '' && str_starts_with( $chosen_shipping_method, $value ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
