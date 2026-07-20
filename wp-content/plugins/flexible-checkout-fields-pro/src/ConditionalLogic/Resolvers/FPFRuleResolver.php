<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Resolvers;

/**
 * RuleResolver for a products (present in cart) that has specific values (added with Flexible Product Fields plugin).
 */
class FPFRuleResolver implements RuleResolver {
	use ResultComparisonAware;

	public function can_resolve(): bool {
		return true;
	}

	public function resolve( array $rules ): bool {
		if ( \WC()->cart->is_empty() ) {
			return false;
		}

		$raw_result = false;
		foreach ( \WC()->cart->get_cart() as $cart_item ) {
			$product_fields = $cart_item['flexible_product_fields'] ?? [];
			foreach ( $product_fields as $field ) {
				if ( $field['name'] === $rules['selection'] ) {
					$raw_result = in_array( $field['value'], $rules['values'], true );
				}
			}
		}

		return $this->get_result_by_comparison( $raw_result, $rules['comparison'] );
	}
}
