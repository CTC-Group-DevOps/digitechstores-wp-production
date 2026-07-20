<?php

trait WC_Conditional_Content_Product_Rule_Helper {

	/**
	 * Resolve the current product from arguments, global context, or the loop ID.
	 */
	protected function resolve_product( $arguments ): ?WC_Product {
		if ( $arguments instanceof WC_Product ) {
			return $arguments;
		}

		if ( is_array( $arguments ) ) {
			if ( isset( $arguments['product'] ) && $arguments['product'] instanceof WC_Product ) {
				return $arguments['product'];
			}

			if ( isset( $arguments['product_id'] ) ) {
				$product = wc_get_product( $arguments['product_id'] );
				if ( $product instanceof WC_Product ) {
					return $product;
				}
			}
		}

		if ( isset( $GLOBALS['product'] ) && $GLOBALS['product'] instanceof WC_Product ) {
			return $GLOBALS['product'];
		}

		$product_id = get_the_ID();
		if ( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product instanceof WC_Product ) {
				return $product;
			}
		}

		return null;
	}
}

class WC_Conditional_Content_Rule_Product_Select extends WC_Conditional_Content_Rule_Base {

	use WC_Conditional_Content_Product_Rule_Helper;

	public function __construct() {
		parent::__construct( 'product_select' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'in'    => __( "is", 'wc_conditional_content' ),
			'notin' => __( "is not", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_condition_input_type(): string {
		return 'Product_Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$product = $this->resolve_product( $arguments );
		if ( ! $product || ! isset( $rule_data['condition'], $rule_data['operator'] ) || ! is_array( $rule_data['condition'] ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$in     = in_array( $product->get_id(), $rule_data['condition'] );
		$result = $rule_data['operator'] == 'in' ? $in : ! $in;

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Product_Type extends WC_Conditional_Content_Rule_Base {

	use WC_Conditional_Content_Product_Rule_Helper;

	public function __construct() {
		parent::__construct( 'product_type' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'in'    => __( "is", 'wc_conditional_content' ),
			'notin' => __( "is not", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {
		$result = array();

		$terms = wc_conditional_content_get_product_types();
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type(): string {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {

		$product = $this->resolve_product( $arguments );
		if ( ! $product || ! isset( $rule_data['condition'], $rule_data['operator'] ) || ! is_array( $rule_data['condition'] ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$product_types = wp_get_post_terms( $product->get_id(), 'product_type', array( 'fields' => 'ids' ) );
		if ( $product_types && ! is_wp_error( $product_types ) ) {
			$in     = count( array_intersect( $product_types, $rule_data['condition'] ) ) > 0;
			$result = $rule_data['operator'] == 'in' ? $in : ! $in;
			return $this->return_is_match( $result, $rule_data, $arguments );
		}

		return $this->return_is_match( false, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Product_Category extends WC_Conditional_Content_Rule_Base {

	use WC_Conditional_Content_Product_Rule_Helper;

	public function __construct() {
		parent::__construct( 'product_category' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'in'    => __( "in", 'wc_conditional_content' ),
			'notin' => __( "not in", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {
		$result = array();

		$terms = wc_conditional_content_get_all_product_categories();
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type(): string {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$product = $this->resolve_product( $arguments );
		if ( ! $product || ! isset( $rule_data['condition'], $rule_data['operator'] ) || ! is_array( $rule_data['condition'] ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$terms = $product->get_category_ids();
		if ( $terms && ! is_wp_error( $terms ) && is_array( $terms ) ) {
			$in     = count( array_intersect( $terms, $rule_data['condition'] ) ) > 0;
			$result = $rule_data['operator'] == 'in' ? $in : ! $in;
			return $this->return_is_match( $result, $rule_data, $arguments );
		}

		return $this->return_is_match( false, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Product_Attribute extends WC_Conditional_Content_Rule_Base {

	use WC_Conditional_Content_Product_Rule_Helper;

	public function __construct() {
		parent::__construct( 'product_attribute' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'in'    => __( "has", 'wc_conditional_content' ),
			'notin' => __( "does not have", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {
		global $woocommerce;

		$result = array();

		$attribute_taxonomies = WC_Conditional_Content_Compatibility::wc_get_attribute_taxonomies();

		if ( $attribute_taxonomies ) {
			//usort($attribute_taxonomies, array(&$this, 'sort_attribute_taxonomies'));

			foreach ( $attribute_taxonomies as $tax ) {
				$attribute_taxonomy_name = WC_Conditional_Content_Compatibility::wc_attribute_taxonomy_name( $tax->attribute_name );
				if ( taxonomy_exists( $attribute_taxonomy_name ) ) {
					$terms = get_terms( $attribute_taxonomy_name, 'orderby=name&hide_empty=0' );
					if ( $terms && !is_wp_error($terms) ) {
						foreach ( $terms as $term ) {
							$result[ $attribute_taxonomy_name . '|' . $term->term_id ] = $tax->attribute_name . ': ' . $term->name;
						}
					}
				}
			}
		}

		return $result;
	}

	public function get_condition_input_type(): string {
		return 'Chosen_Select';
	}

	public function sort_attribute_taxonomies( $taxa, $taxb ) {
		return strcmp( $taxa->attribute_name, $taxb->attribute_name );
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$product = $this->resolve_product( $arguments );
		if ( ! $product || ! isset( $rule_data['condition'], $rule_data['operator'] ) || ! is_array( $rule_data['condition'] ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		foreach ( $rule_data['condition'] as $condition ) {
			$term_data = explode( '|', $condition );
			if ( count( $term_data ) < 2 ) {
				continue;
			}

			$attribute_taxonomy_name = $term_data[0];
			$term_id                 = $term_data[1];

			$post_terms = wp_get_post_terms( $product->get_id(), $attribute_taxonomy_name, array( 'fields' => 'ids' ) );
			if ( $post_terms && ! is_wp_error( $post_terms ) ) {
				$in     = in_array( $term_id, $post_terms );
				$result = $rule_data['operator'] == 'in' ? $in : ! $in;
				return $this->return_is_match( $result, $rule_data, $arguments );
			}
		}

		return $this->return_is_match( false, $rule_data, $arguments );
	}

}


class WC_Conditional_Content_Rule_Product_Tag extends WC_Conditional_Content_Rule_Base {

	use WC_Conditional_Content_Product_Rule_Helper;

	public function __construct() {
		parent::__construct( 'product_tag' );
	}

	public function get_possible_rule_operators(): array {
		$operators = array(
			'in'    => __( "in", 'wc_conditional_content' ),
			'notin' => __( "not in", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possible_rule_values(): array {
		$result = array();

		$terms = wc_conditional_content_get_all_product_tags();
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type(): string {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$product = $this->resolve_product( $arguments );
		if ( ! $product || ! isset( $rule_data['condition'], $rule_data['operator'] ) || ! is_array( $rule_data['condition'] ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$terms = $product->get_tag_ids();
		if ( $terms && ! is_wp_error( $terms ) && is_array( $terms ) ) {
			$in     = count( array_intersect( $terms, $rule_data['condition'] ) ) > 0;
			$result = $rule_data['operator'] == 'in' ? $in : ! $in;
			return $this->return_is_match( $result, $rule_data, $arguments );
		}

		return $this->return_is_match( false, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Product_Price extends WC_Conditional_Content_Rule_Base {

	use WC_Conditional_Content_Product_Rule_Helper;

	public function __construct() {
		parent::__construct( 'product_price' );
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
		$product = $this->resolve_product( $arguments );
		if ( ! $product || ! isset( $rule_data['condition'], $rule_data['operator'] ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$price = $product->get_price();
		$value = (float) $rule_data['condition'];
		$result = false;

		switch ( $rule_data['operator'] ) {
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

class WC_Conditional_Content_Rule_Product_Custom_Field extends WC_Conditional_Content_Rule_Base {

	use WC_Conditional_Content_Product_Rule_Helper;

	public function __construct() {
		parent::__construct( 'product_custom_field' );
	}

	public function get_possible_rule_operators(): array {
		return array(
			'empty' => __( "is empty", 'wc_conditional_content' ),
			'notempty' => __( "is not empty", 'wc_conditional_content' ),
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
			'>'  => __( "is greater than", 'wc_conditional_content' ),
			'<'  => __( "is less than", 'wc_conditional_content' ),
			'>=' => __( "is greater or equal to", 'wc_conditional_content' ),
			'=<' => __( "is less or equal to", 'wc_conditional_content' )
		);
	}

	public function get_condition_input_type(): string {
		return 'Text_Text';
	}

	public function is_match( $rule_data, $arguments = null ): bool {
		$product = $this->resolve_product( $arguments );
		if ( ! $product || ! isset( $rule_data['condition'], $rule_data['operator'] ) || ! is_array( $rule_data['condition'] ) ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$field_key = $rule_data['condition']['field_key'] ?? null;
		$value     = $rule_data['condition']['field_value'] ?? null;

		if ( $field_key === null ) {
			return $this->return_is_match( false, $rule_data, $arguments );
		}

		$custom_field_value = $product->get_meta( $field_key, true );
		if ( $custom_field_value === '' ) {
			$custom_field_value = get_post_meta( $product->get_id(), $field_key, true );
		}

		$custom_field_value = apply_filters( 'wc_conditional_content_product_custom_field_value', $custom_field_value, $field_key, $product );
		$value             = apply_filters( 'wc_conditional_content_product_custom_field_value_to_match', $value, $field_key, $product, $custom_field_value );
		$result            = false;

		switch ( $rule_data['operator'] ) {
			case 'empty' :
				$result = empty( $custom_field_value );
				break;
			case 'notempty' :
				$result = ! empty( $custom_field_value );
				break;
			case '==' :
				$result = $custom_field_value == $value;
				break;
			case '!=' :
				$result = $custom_field_value != $value;
				break;
			case '>' :
				$result = $custom_field_value > $value;
				break;
			case '<' :
				$result = $custom_field_value < $value;
				break;
			case '>=' :
				$result = $custom_field_value >= $value;
				break;
			case '<=' :
				$result = $custom_field_value <= $value;
				break;
			default:
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}
