<?php

namespace WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;

use WPDesk\FCF\Free\Collections\RouteParamBag;

/**
 * Options provider for cart contains (category) rules.
 */
class CartContains extends OptionsProvider {

	public function get_label(): string {
		return __( 'Cart contains', 'flexible-checkout-fields-pro' );
	}

	public function get_value(): string {
		return 'cart_contains';
	}

	public function get_selections( RouteParamBag $params ): array {
		$items = [
			'product'          => __( 'Product', 'flexible-checkout-fields-pro' ),
			'product_category' => __( 'Category', 'flexible-checkout-fields-pro' ),
			'product_type'     => __( 'Product type', 'flexible-checkout-fields-pro' ),
			'items_in_cart'    => __( 'Number of items', 'flexible-checkout-fields-pro' ),
		];

		return $items;
	}

	public function get_comparisons( RouteParamBag $params ): array {
		$selection = $params->collection( 'form_values' )->getString( 'selection' );

		if ( 'items_in_cart' === $selection ) {
			return [
				'is'        => __( 'Is', 'flexible-checkout-fields-pro' ),
				'more_than' => __( 'More than', 'flexible-checkout-fields-pro' ),
				'less_than' => __( 'Less than', 'flexible-checkout-fields-pro' ),
			];
		}

		return [
			'which_is'     => __( 'Which is', 'flexible-checkout-fields-pro' ),
			'which_is_not' => __( 'Which is not', 'flexible-checkout-fields-pro' ),
		];
	}

	public function get_values( RouteParamBag $params ): array {
		$selection    = $params->collection( 'form_values' )->getString( 'selection' );
		$field_search = $params->getString( 'field_search' );

		switch ( $selection ) {
			case 'product':
				return $this->get_products_values( $field_search );
			case 'product_category':
				return $this->get_product_categories_values( $field_search );
			case 'product_type':
				return $this->get_product_types_values();
			default:
				return [];
		}
	}

	/**
	 * Retrieves an array of product values.
	 *
	 * @return array<int, string> An array of product IDs and titles.
	 */
	private function get_products_values( string $field_search ): array {
		$args = [
			'posts_per_page' => -1,
			'post_type'      => [ 'product', 'product_variation' ],
			'orderby'        => 'title',
			'order'          => 'ASC',
			'lang'           => '',
		];

		if ( $field_search ) {
			$args['s'] = $field_search;
		}

		$posts = get_posts( $args );

		$values = [];
		foreach ( $posts as $post ) {
			$values[ $post->ID ] = sprintf( '%s (#%d)', $post->post_title, $post->ID );
		}
		return $values;
	}

	/**
	 * Retrieves an array of product category values.
	 *
	 * @return array<int, string> An associative array of category IDs and names with their corresponding term IDs.
	 */
	private function get_product_categories_values( string $field_search ): array {
		$wpml_integration = $this->get_integration_wpml();
		if ( $wpml_integration ) {
			remove_filter( 'get_terms_args', [ $wpml_integration, 'get_terms_args_filter' ] );
			remove_filter( 'get_term', [ $wpml_integration, 'get_term_adjust_id' ] );
			remove_filter( 'terms_clauses', [ $wpml_integration, 'terms_clauses' ] );
			remove_filter( 'get_term', [ $wpml_integration, 'get_term_adjust_id' ], 1, 1 );
		}

		$args = [
			'taxonomy'   => 'product_cat',
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => false,
		];

		if ( $field_search ) {
			$args['search'] = $field_search;
		}

		$cats = get_terms( $args );

		if ( $wpml_integration ) {
			add_filter( 'terms_clauses', [ $wpml_integration, 'terms_clauses' ] );
			add_filter( 'get_term', [ $wpml_integration, 'get_term_adjust_id' ] );
			add_filter( 'get_terms_args', [ $wpml_integration, 'get_terms_args_filter' ] );
			add_filter( 'get_term', [ $wpml_integration, 'get_term_adjust_id' ], 1, 1 );
		}

		$values = [];
		foreach ( $cats as $cat ) {
			$values[ $cat->term_id ] = sprintf( '%s (#%d)', $cat->name, $cat->term_id );
		}
		return $values;
	}

	/**
	 * Retrieves an array of product types and their corresponding labels.
	 *
	 * @return array<string, string> An array where the keys are the product types and the values are their labels.
	 */
	private function get_product_types_values(): array {
		$types  = \wc_get_product_types();
		$values = [];
		foreach ( $types as $type => $label ) {
			$values[ $type ] = $label;
		}
		return $values;
	}

	/**
	 * Returns integration object with WPML plugin.
	 *
	 * @return object|null Main SitePress Class.
	 */
	private function get_integration_wpml() {
		global $sitepress;
		if ( ! $sitepress || ! is_object( $sitepress )
			|| ! method_exists( $sitepress, 'get_terms_args_filter' )
			|| ! method_exists( $sitepress, 'get_term_adjust_id' )
			|| ! method_exists( $sitepress, 'terms_clauses' )
			|| ! method_exists( $sitepress, 'get_term_adjust_id' ) ) {
			return null;
		}
		return $sitepress;
	}
}
