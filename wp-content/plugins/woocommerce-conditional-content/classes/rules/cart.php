<?php

class WC_Conditional_Content_Rule_Cart_Total extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'cart_total' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
			'>'  => __( "is greater than", 'wc_conditional_content' ),
			'<'  => __( "is less than", 'wc_conditional_content' ),
			'>=' => __( "is greater or equal to", 'wc_conditional_content' ),
			'=<' => __( "is less or equal to", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_condition_input_type(): string {
		return 'Text';
	}

	public function is_match( $rule_data, $arguments = null ): bool {

		$result = false;
		if ( !wc_prices_include_tax() ) {
			$price = WC()->cart->get_cart_contents_total();
		} else {
			$price = WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax();
		}

		$value = (float) ( $rule_data['condition'] ?? 0 );
		switch ( $rule_data['operator'] ?? '' ) {
			case '==' :
				$result = $price == $value;
				break;
			case '!=' :
				$result = $price != $value;
				break;
			case '>' :
				$result = $price > $value;
				break;
			case '<' :
				$result = $price < $value;
				break;
			case '>=' :
				$result = $price >= $value;
				break;
			// The admin UI has offered '=<' for "is less or equal to" since the
			// plugin's first commit, while this switch only ever handled '<='.
			// Selecting it therefore fell through to default and never matched.
			// Both spellings are accepted rather than renaming the key: an
			// existing rule stored as '=<' would render a <select> with no
			// matching option, the browser would select the first one ('=='),
			// and the merchant's rule would silently change meaning on their
			// next save.
			case '=<' :
			case '<=' :
				$result = $price <= $value;
				break;
			default:
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Cart_Quantity extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'cart_quantity' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
			'>'  => __( "is greater than", 'wc_conditional_content' ),
			'<'  => __( "is less than", 'wc_conditional_content' ),
			'>=' => __( "is greater or equal to", 'wc_conditional_content' ),
			'=<' => __( "is less or equal to", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_condition_input_type(): string {
		return 'Text';
	}

	public function is_match( $rule_data, $arguments = null ): bool {

		$cart_contents = WC()->cart->get_cart();
		$found_quantity = 0;
		if ( $cart_contents && count( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				$found_quantity += $cart_item['quantity'];
			}
		}

		$value = (float) ( $rule_data['condition'] ?? 0 );
		switch ( $rule_data['operator'] ?? '' ) {
			case '==' :
				$result = $found_quantity == $value;
				break;
			case '!=' :
				$result = $found_quantity != $value;
				break;
			case '>' :
				$result = $found_quantity > $value;
				break;
			case '<' :
				$result = $found_quantity < $value;
				break;
			case '>=' :
				$result = $found_quantity >= $value;
				break;
			case '=<' :
			case '<=' :
				$result = $found_quantity <= $value;
				break;
			default:
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Cart_Product extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'cart_product' );
	}

	public function get_possible_rule_operators(): array {

		$operators = array(
			'<'  => __( "contains less than", 'wc_conditional_content' ),
			'>'  => __( "contains at least", 'wc_conditional_content' ),
			'==' => __( "contains exactly", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_condition_input_type(): string {
		return 'Cart_Product_Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$result        = false;
		$cart_contents = WC()->cart->get_cart();

		if ( ! isset( $rule_data['condition'] ) || ! is_array( $rule_data['condition'] ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$products = $rule_data['condition']['products'] ?? array();
		$quantity = $rule_data['condition']['qty'] ?? 0;
		$type     = $rule_data['operator'] ?? '';

		if ( ! is_array( $products ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$found_quantity = 0;

		if ( $cart_contents && count( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( in_array( '0', $products ) ) {
					$found_quantity += $cart_item['quantity'];
				} elseif ( in_array( $cart_item['product_id'], $products ) || ( isset( $cart_item['variation_id'] ) && in_array( $cart_item['variation_id'], $products ) ) ) {
					$found_quantity += $cart_item['quantity'];
				}
			}
		}

		switch ( $type ) {
			case '<' :
				// Was >=, so a cart holding exactly 3 satisfied "contains less
				// than 3". cart_category has always used > for the same label.
				$result = $quantity > $found_quantity;
				break;
			case '>' :
				$result = $quantity <= $found_quantity;
				break;
			case '==' :
				$result = $quantity == $found_quantity;
				break;
			default :
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Cart_Category extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'cart_category' );
	}

	public function get_possible_rule_operators(): array {

		$operators = array(
			'<'  => __( "contains less than", 'wc_conditional_content' ),
			'>'  => __( "contains at least", 'wc_conditional_content' ),
			'==' => __( "contains exactly", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {
		$result = array();

		$terms = wc_conditional_content_get_all_product_categories();
		if ( $terms && !is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type(): string {
		return 'Cart_Category_Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$result        = false;
		$cart_contents = WC()->cart->get_cart();

		if ( ! isset( $rule_data['condition'] ) || ! is_array( $rule_data['condition'] ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$categories = $rule_data['condition']['categories'] ?? array();
		$quantity   = $rule_data['condition']['qty'] ?? 0;
		$type       = $rule_data['operator'] ?? '';

		if ( ! is_array( $categories ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$found_quantity = 0;

		if ( $cart_contents && count( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				$product = $cart_item['data'];

				if ( $product->is_type( 'variation' ) ) {
					$product = wc_get_product( $product->get_parent_id() );
				}

				$terms = $product->get_category_ids();
				if ( $terms && !is_wp_error( $terms ) && count( array_intersect( $terms, $categories ) ) > 0 ) {
					$found_quantity += $cart_item['quantity'];
				}
			}
		}

		switch ( $type ) {
			case '<' :
				$result = $quantity > $found_quantity;
				break;
			case '>' :
				$result = $quantity <= $found_quantity;
				break;
			case '==' :
				$result = $quantity == $found_quantity;
				break;
			default :
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Cart_Line_Item_Product extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'cart_line_item_product' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'in'    => __( "in", 'wc_conditional_content' ),
			'notin' => __( "not in", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_condition_input_type(): string {
		return 'Product_Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$result  = false;
		$product = !empty( $arguments ) && isset( $arguments[0] ) && isset( $arguments[0]['data'] ) ? $arguments[0]['data'] : false;
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$in     = in_array( $product->get_id(), $rule_data['condition'] );
			$result = $rule_data['operator'] == 'in' ? $in : !$in;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Cart_Line_Item_Quantity extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'cart_line_item_quantity' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
			'>'  => __( "is greater than", 'wc_conditional_content' ),
			'<'  => __( "is less than", 'wc_conditional_content' ),
			'>=' => __( "is greater or equal to", 'wc_conditional_content' ),
			'=<' => __( "is less or equal to", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_condition_input_type(): string {
		return 'Text';
	}


	public function is_match( $rule_data, $arguments = null ): bool {
		$result         = false;
		$quantity       = $rule_data['condition'] ?? 0; //The quantity input.
		$type           = $rule_data['operator'] ?? '';
		$found_quantity = 0;
		if ( !empty( $arguments ) && isset( $arguments[0] ) && isset( $arguments[0]['quantity'] ) ) {
			$found_quantity = $arguments[0]['quantity'];
		}

		switch ( $type ) {
			case '==' :
				$result = $found_quantity == $quantity;
				break;
			case '!=' :
				$result = $found_quantity != $quantity;
				break;
			case '>' :
				$result = $found_quantity > $quantity;
				break;
			case '<' :
				$result = $found_quantity < $quantity;
				break;
			case '>=' :
				$result = $found_quantity >= $quantity;
				break;
			case '=<' :
			case '<=' :
				$result = $found_quantity <= $quantity;
				break;
			default:
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}
