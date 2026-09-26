<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Resolvers;

/**
 * RuleResolver for cart rules.
 */
class CartRuleResolver implements RuleResolver {
	use ResultComparisonAware;

	/**
	 * Term name for product categories.
	 *
	 * @var string
	 */
	const TERM_PRODUCT_CAT = 'product_cat';

	public function can_resolve(): bool {
		return true;
	}

	public function resolve( array $rule ): bool {
		if ( \WC()->cart->is_empty() ) {
			return false;
		}

		if ( 'product' === $rule['selection'] ) {
			return $this->has_products_in_cart( $rule );
		}

		if ( 'product_category' === $rule['selection'] ) {
			return $this->has_categories_in_cart( $rule );
		}

		if ( 'product_type' === $rule['selection'] ) {
			return $this->has_product_types_in_cart( $rule );
		}

		if ( 'items_in_cart' === $rule['selection'] ) {
			return $this->has_items_in_cart( $rule );
		}

		if ( 'cart_total' === $rule['selection'] ) {
			return $this->has_cart_total( $rule );
		}

		return false;
	}

	/**
	 * Check if products are in the cart.
	 *
	 * @param array<string, mixed> $rule Conditional logic rule.
	 * @return bool
	 */
	private function has_products_in_cart( array $rule ): bool {
		$product_ids = [];
		foreach ( \WC()->cart->get_cart() as $cart_item ) {
			/**
			 * @var WC_Product $_product
			 */
			$_product = $cart_item['data'];

			$product_ids[] = (string) $_product->get_id();

			if ( $_product->is_type( 'variation' ) ) {
				$product_ids[] = (string) $_product->get_parent_id();
			}
		}

		$raw_result = is_array( $rule['values'] )
			? $this->has_same_values( $product_ids, $rule['values'] )
			: false;

		return $this->get_result_by_comparison( $raw_result, $rule['comparison'] );
	}

	/**
	 * Check if products with specified categories are in the cart.
	 *
	 * @param array<string, mixed> $rule Conditional logic rule.
	 * @return bool
	 */
	private function has_categories_in_cart( array $rule ): bool {
		$categories = [];
		foreach ( \WC()->cart->get_cart() as $cart_item ) {
			/**
			 * @var WC_Product $_product
			 */
			$_product = $cart_item['data'];

			$product_id  = $_product->is_type( 'variation' ) ? $_product->get_parent_id() : $_product->get_id();
			$_categories = \get_the_terms( $product_id, self::TERM_PRODUCT_CAT );

			if ( ! is_array( $_categories ) ) {
				continue;
			}

			foreach ( $_categories as $_category ) {
				$categories[] = (string) $_category->term_id;
			}
		}

		$raw_result = is_array( $rule['values'] )
			? $this->has_same_values( $categories, $rule['values'] )
			: false;

		return $this->get_result_by_comparison( $raw_result, $rule['comparison'] );
	}

	/**
	 * Checks if the products in the cart have certain types.
	 *
	 * @param array<string, mixed> $rule Conditional logic rule.
	 * @return bool
	 */
	private function has_product_types_in_cart( array $rule ): bool {
		$product_types = [];
		foreach ( \WC()->cart->get_cart() as $cart_item ) {
			/**
			 * @var WC_Product $_product
			 */
			$_product = $cart_item['data'];
			$_product = $_product instanceof \WC_Product_Variation ?
				new \WC_Product_Variable( $_product->get_parent_id() ) :
				$_product;

			$product_types[] = $_product->get_type();
		}

		$raw_result = is_array( $rule['values'] )
			? $this->has_same_values( $product_types, $rule['values'] )
			: false;

		return $this->get_result_by_comparison( $raw_result, $rule['comparison'] );
	}


	/**
	 * Check if the number of items in the cart meet a certain condition.
	 *
	 * @param array<string, mixed> $rule Conditional logic rule.
	 * @return bool
	 */
	private function has_items_in_cart( array $rule ): bool {
		$items_in_cart = WC()->cart->get_cart_contents_count();
		$rule_value    = (int) $rule['values'];

		switch ( $rule['comparison'] ) {
			case 'is':
				return $items_in_cart === $rule_value;
			case 'more_than':
				return $items_in_cart > $rule_value;
			case 'less_than':
				return $items_in_cart < $rule_value;
			default:
				return false;
		}
	}


	/**
	 * Checks if the cart total condition is met.
	 *
	 * @param array<string, mixed> $rule Conditional logic rule.
	 * @return bool
	 */
	private function has_cart_total( array $rule ): bool {
		$cart_total = (float) \WC()->cart->get_total( 'raw' );
		$rule_value = (float) $rule['values'];

		switch ( $rule['comparison'] ) {
			case 'is':
				return $cart_total === $rule_value;
			case 'more_than':
				return $cart_total > $rule_value;
			case 'less_than':
				return $cart_total < $rule_value;
			default:
				return false;
		}
	}
}
