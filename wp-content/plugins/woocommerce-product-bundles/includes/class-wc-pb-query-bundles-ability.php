<?php
/**
 * WC_PB_Query_Bundles_Ability class
 *
 * @package WooCommerce Product Bundles
 * @since   8.5.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_PB_Query_Bundles_Ability ability definition.
 *
 * @version 8.5.9
 */
class WC_PB_Query_Bundles_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Product Bundles ability category.
	 */
	const CATEGORY = 'woocommerce';

	/**
	 * Product Bundles query ability ID.
	 */
	const NAME = 'woocommerce-product-bundles/query-bundles';

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return self::NAME;
	}

	/**
	 * Get the ability registration arguments.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Query product bundles', 'woocommerce-product-bundles' ),
			'description'         => __( 'Find product bundles by ID or common bundle filters, including bundled item configuration.', 'woocommerce-product-bundles' ),
			'category'            => self::CATEGORY,
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'query_bundles' ),
			'permission_callback' => array( __CLASS__, 'can_query_bundles' ),
			'meta'                => array(
				'show_in_rest' => true,
				'mcp'          => array(
					'public' => true,
					'type'   => 'tool',
				),
				'annotations'  => array(
					'readonly'    => true,
					'destructive' => false,
					'idempotent'  => true,
				),
			),
		);
	}

	/**
	 * Query product bundles.
	 *
	 * @param  mixed $input Ability input.
	 * @return array|WP_Error
	 */
	public static function query_bundles( $input = array() ) {
		$input = is_array( $input ) ? $input : array();

		if ( ! empty( $input['id'] ) ) {
			$product = wc_get_product( absint( $input['id'] ) );

			if ( ! $product || ! $product->is_type( 'bundle' ) ) {
				return new WP_Error(
					'woocommerce_product_bundles_invalid_bundle',
					__( 'Bundle not found.', 'woocommerce-product-bundles' ),
					array( 'status' => 404 )
				);
			}

			return array(
				'bundles'     => array( self::format_bundle_for_response( $product ) ),
				'total_pages' => 1,
				'page'        => 1,
				'per_page'    => 1,
			);
		}

		$page     = isset( $input['page'] ) ? absint( $input['page'] ) : 1;
		$per_page = isset( $input['per_page'] ) ? absint( $input['per_page'] ) : 10;

		$args = array(
			'type'     => 'bundle',
			'limit'    => $per_page,
			'page'     => $page,
			'paginate' => true,
			'return'   => 'objects',
			'orderby'  => 'ID',
			'order'    => 'ASC',
		);

		foreach ( array( 'status', 'sku', 'stock_status' ) as $field ) {
			if ( ! empty( $input[ $field ] ) ) {
				$args[ $field ] = wc_clean( $input[ $field ] );
			}
		}

		if ( ! empty( $input['search'] ) ) {
			$args['s'] = wc_clean( $input['search'] );
		}

		if ( ! empty( $input['contains_product_id'] ) ) {
			$bundle_ids = self::get_bundle_ids_containing_product( absint( $input['contains_product_id'] ) );

			if ( empty( $bundle_ids ) ) {
				return array(
					'bundles'     => array(),
					'total_pages' => 0,
					'page'        => $page,
					'per_page'    => $per_page,
				);
			}

			$args['include'] = $bundle_ids;
		}

		$results     = wc_get_products( $args );
		$bundles     = is_object( $results ) && isset( $results->products ) ? $results->products : array();
		$total_pages = is_object( $results ) && isset( $results->max_num_pages ) ? absint( $results->max_num_pages ) : ( count( $bundles ) > 0 ? 1 : 0 );

		return array(
			'bundles'     => array_map( array( __CLASS__, 'format_bundle_for_response' ), $bundles ),
			'total_pages' => $total_pages,
			'page'        => $page,
			'per_page'    => $per_page,
		);
	}

	/**
	 * Check product bundle query permissions.
	 *
	 * @param  mixed $input Ability input.
	 * @return bool
	 */
	public static function can_query_bundles( $input = array() ) {
		$input = is_array( $input ) ? $input : array();

		if ( ! empty( $input['contains_product_id'] ) && ! wc_rest_check_post_permissions( 'product', 'read', absint( $input['contains_product_id'] ) ) ) {
			return false;
		}

		$product_id = ! empty( $input['id'] ) ? absint( $input['id'] ) : 0;

		return wc_rest_check_post_permissions( 'product', 'read', $product_id );
	}

	/**
	 * Get bundle IDs containing a product.
	 *
	 * @param  int $product_id Product ID.
	 * @return array
	 */
	private static function get_bundle_ids_containing_product( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return array();
		}

		$bundle_map = WC_PB_DB::query_bundled_items(
			array(
				'product_id' => $product_id,
				'return'     => 'id=>bundle_id',
			)
		);

		return array_values( array_unique( array_map( 'absint', array_values( $bundle_map ) ) ) );
	}

	/**
	 * Format a bundle for the ability response.
	 *
	 * @param  WC_Product_Bundle $product Bundle product.
	 * @return array
	 */
	private static function format_bundle_for_response( $product ) {
		$permalink = $product->get_permalink();

		// The ability exposes store-management configuration, so use edit context rather than display-filtered catalog values.
		return array(
			'id'                    => $product->get_id(),
			'name'                  => $product->get_name(),
			'sku'                   => $product->get_sku(),
			'status'                => $product->get_status(),
			'permalink'             => $permalink ? $permalink : null,
			'catalog_visibility'    => $product->get_catalog_visibility(),
			'stock_status'          => $product->get_stock_status( 'edit' ),
			'bundle_stock_status'   => $product->get_bundle_stock_status( 'edit' ),
			'bundle_stock_quantity' => $product->get_bundle_stock_quantity( 'edit' ),
			'settings'              => array(
				'virtual'                   => $product->get_virtual_bundle( 'edit' ),
				'layout'                    => $product->get_layout( 'edit' ),
				'add_to_cart_form_location' => $product->get_add_to_cart_form_location( 'edit' ),
				'editable_in_cart'          => $product->get_editable_in_cart( 'edit' ),
				'sold_individually_context' => $product->get_sold_individually_context( 'edit' ),
				'item_grouping'             => $product->get_group_mode( 'edit' ),
				'min_size'                  => $product->get_min_bundle_size( 'edit' ),
				'max_size'                  => $product->get_max_bundle_size( 'edit' ),
			),
			'prices'                => self::get_bundle_prices( $product ),
			'bundled_items'         => self::get_bundled_items( $product ),
		);
	}

	/**
	 * Get curated bundle prices for the ability response.
	 *
	 * @param  WC_Product_Bundle $product Bundle product.
	 * @return array
	 */
	private static function get_bundle_prices( $product ) {
		return array(
			'price'         => array(
				'min' => array(
					'incl_tax' => (string) $product->get_bundle_price_including_tax( 'min' ),
					'excl_tax' => (string) $product->get_bundle_price_excluding_tax( 'min' ),
				),
				'max' => array(
					'incl_tax' => (string) $product->get_bundle_price_including_tax( 'max' ),
					'excl_tax' => (string) $product->get_bundle_price_excluding_tax( 'max' ),
				),
			),
			'regular_price' => array(
				'min' => array(
					'incl_tax' => (string) $product->get_bundle_regular_price_including_tax( 'min' ),
					'excl_tax' => (string) $product->get_bundle_regular_price_excluding_tax( 'min' ),
				),
				'max' => array(
					'incl_tax' => (string) $product->get_bundle_regular_price_including_tax( 'max' ),
					'excl_tax' => (string) $product->get_bundle_regular_price_excluding_tax( 'max' ),
				),
			),
		);
	}

	/**
	 * Get curated bundled item data for the ability response.
	 *
	 * @param  WC_Product_Bundle $bundle Bundle product.
	 * @return array
	 */
	private static function get_bundled_items( $bundle ) {
		$items = array();

		foreach ( $bundle->get_bundled_data_items( 'edit' ) as $data_item ) {
			$items[] = self::format_bundled_item_for_response( $data_item, $bundle );
		}

		return $items;
	}

	/**
	 * Format a bundled item for the ability response.
	 *
	 * @param  WC_Bundled_Item_Data $data_item Bundled item data.
	 * @param  WC_Product_Bundle    $bundle    Bundle product.
	 * @return array
	 */
	private static function format_bundled_item_for_response( $data_item, $bundle ) {
		$bundled_item = wc_pb_get_bundled_item( $data_item, $bundle );

		if ( ! $bundled_item ) {
			return self::format_unavailable_bundled_item_for_response( $data_item );
		}

		$product = $bundled_item->get_product();
		$data    = $bundled_item->get_data();

		return array(
			'bundled_item_id'                       => $data_item->get_id(),
			'product_id'                            => $bundled_item->get_product_id(),
			'product_exists'                        => true,
			'product_name'                          => $product->get_name(),
			'product_sku'                           => $product->get_sku(),
			'product_status'                        => $product->get_status(),
			'menu_order'                            => $data_item->get_menu_order(),
			'quantity_min'                          => $bundled_item->get_quantity(),
			'quantity_max'                          => $bundled_item->get_quantity( 'max' ),
			'quantity_default'                      => $bundled_item->get_quantity( 'default' ),
			'priced_individually'                   => $bundled_item->is_priced_individually(),
			'shipped_individually'                  => $bundled_item->is_shipped_individually(),
			'override_title'                        => $bundled_item->has_title_override(),
			'title'                                 => is_scalar( $data['title'] ) ? (string) $data['title'] : '',
			'override_description'                  => 'yes' === $data['override_description'],
			'description'                           => is_scalar( $data['description'] ) ? (string) $data['description'] : '',
			'optional'                              => $bundled_item->is_optional(),
			'hide_thumbnail'                        => ! $bundled_item->is_thumbnail_visible(),
			'discount'                              => is_scalar( $data['discount'] ) ? (string) $data['discount'] : '',
			'override_variations'                   => 'yes' === $data['override_variations'],
			'allowed_variations'                    => wp_parse_id_list( false === $data['allowed_variations'] ? array() : $data['allowed_variations'] ),
			'override_default_variation_attributes' => 'yes' === $data['override_default_variation_attributes'],
			'default_variation_attributes'          => WC_PB_Bundled_Item_Formatters::get_bundled_item_attribute_defaults( $data_item ),
			'single_product_visibility'             => $bundled_item->is_visible( 'product' ) ? 'visible' : 'hidden',
			'cart_visibility'                       => $bundled_item->is_visible( 'cart' ) ? 'visible' : 'hidden',
			'order_visibility'                      => $bundled_item->is_visible( 'order' ) ? 'visible' : 'hidden',
			'single_product_price_visibility'       => $bundled_item->is_price_visible( 'product' ) ? 'visible' : 'hidden',
			'cart_price_visibility'                 => $bundled_item->is_price_visible( 'cart' ) ? 'visible' : 'hidden',
			'order_price_visibility'                => $bundled_item->is_price_visible( 'order' ) ? 'visible' : 'hidden',
			'stock_status'                          => WC_PB_Bundled_Item_Formatters::get_bundled_item_stock_status( $data_item, $bundle, 'unavailable' ),
		);
	}

	/**
	 * Format an unavailable bundled item for the ability response.
	 *
	 * @param  WC_Bundled_Item_Data $data_item Bundled item data.
	 * @return array
	 */
	private static function format_unavailable_bundled_item_for_response( $data_item ) {
		return array(
			'bundled_item_id' => $data_item->get_id(),
			'product_id'      => $data_item->get_product_id(),
			'product_exists'  => false,
			'product_name'    => '',
			'product_sku'     => '',
			'product_status'  => '',
			'menu_order'      => $data_item->get_menu_order(),
			'stock_status'    => 'unavailable',
		);
	}

	/**
	 * Get query-bundles input schema.
	 *
	 * @return array
	 */
	private static function get_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'                  => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'Return one bundle by product ID.', 'woocommerce-product-bundles' ),
				),
				'contains_product_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'Limit results to bundles that contain this product ID.', 'woocommerce-product-bundles' ),
				),
				'search'              => array(
					'type'        => 'string',
					'description' => __( 'Search bundle names and descriptions.', 'woocommerce-product-bundles' ),
				),
				'sku'                 => array(
					'type'        => 'string',
					'description' => __( 'Limit results to bundles matching this SKU.', 'woocommerce-product-bundles' ),
				),
				'status'              => array(
					'type'        => 'string',
					'enum'        => self::get_product_status_values(),
					'description' => __( 'Limit results to bundles with this product status.', 'woocommerce-product-bundles' ),
				),
				'stock_status'        => array(
					'type'        => 'string',
					'enum'        => array_keys( wc_get_product_stock_status_options() ),
					'description' => __( 'Limit results to bundles with this parent bundle stock status.', 'woocommerce-product-bundles' ),
				),
				'page'                => array(
					'type'        => 'integer',
					'default'     => 1,
					'minimum'     => 1,
					'description' => __( 'Result page for collection queries.', 'woocommerce-product-bundles' ),
				),
				'per_page'            => array(
					'type'        => 'integer',
					'default'     => 10,
					'minimum'     => 1,
					'maximum'     => 100,
					'description' => __( 'Maximum number of bundles to return for collection queries.', 'woocommerce-product-bundles' ),
				),
			),
			'additionalProperties' => false,
			'default'              => array(),
		);
	}

	/**
	 * Get query-bundles output schema.
	 *
	 * @return array
	 */
	private static function get_output_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'bundles'     => array(
					'type'        => 'array',
					'description' => __( 'Product bundles matching the query.', 'woocommerce-product-bundles' ),
					'items'       => self::get_bundle_output_schema(),
				),
				'total_pages' => array(
					'type'        => 'integer',
					'description' => __( 'Total pages available for this query.', 'woocommerce-product-bundles' ),
				),
				'page'        => array(
					'type'        => 'integer',
					'description' => __( 'Current result page.', 'woocommerce-product-bundles' ),
				),
				'per_page'    => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of bundles returned per page.', 'woocommerce-product-bundles' ),
				),
			),
			'required'             => array( 'bundles', 'total_pages', 'page', 'per_page' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get bundle output schema.
	 *
	 * @return array
	 */
	private static function get_bundle_output_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'                    => array( 'type' => 'integer' ),
				'name'                  => array( 'type' => 'string' ),
				'sku'                   => array( 'type' => 'string' ),
				'status'                => array(
					'type' => 'string',
					'enum' => self::get_product_status_values(),
				),
				'permalink'             => array(
					'type'   => array( 'string', 'null' ),
					'format' => 'uri',
				),
				'catalog_visibility'    => array(
					'type' => 'string',
					'enum' => array_keys( wc_get_product_visibility_options() ),
				),
				'stock_status'          => array(
					'type' => 'string',
					'enum' => array_keys( wc_get_product_stock_status_options() ),
				),
				'bundle_stock_status'   => array(
					'type' => 'string',
					'enum' => self::get_bundle_stock_status_values(),
				),
				'bundle_stock_quantity' => array(
					'type' => array( 'integer', 'string' ),
				),
				'settings'              => self::get_bundle_settings_schema(),
				'prices'                => self::get_bundle_prices_schema(),
				'bundled_items'         => array(
					'type'  => 'array',
					'items' => self::get_bundled_item_output_schema(),
				),
			),
			'required'             => array( 'id', 'name', 'status', 'settings', 'prices', 'bundled_items' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get bundle settings output schema.
	 *
	 * @return array
	 */
	private static function get_bundle_settings_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'virtual'                   => array( 'type' => 'boolean' ),
				'layout'                    => array(
					'type' => 'string',
					'enum' => array_keys( WC_Product_Bundle::get_layout_options() ),
				),
				'add_to_cart_form_location' => array(
					'type' => 'string',
					'enum' => array_keys( WC_Product_Bundle::get_add_to_cart_form_location_options() ),
				),
				'editable_in_cart'          => array( 'type' => 'boolean' ),
				'sold_individually_context' => array(
					'type' => 'string',
					'enum' => array( 'product', 'configuration' ),
				),
				'item_grouping'             => array(
					'type' => 'string',
					'enum' => array_keys( WC_Product_Bundle::get_group_mode_options() ),
				),
				'min_size'                  => array( 'type' => array( 'integer', 'string' ) ),
				'max_size'                  => array( 'type' => array( 'integer', 'string' ) ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get bundle prices output schema.
	 *
	 * @return array
	 */
	private static function get_bundle_prices_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'price'         => self::get_price_range_schema(),
				'regular_price' => self::get_price_range_schema(),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get price range output schema.
	 *
	 * @return array
	 */
	private static function get_price_range_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'min' => self::get_tax_price_schema(),
				'max' => self::get_tax_price_schema(),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get tax price output schema.
	 *
	 * @return array
	 */
	private static function get_tax_price_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'incl_tax' => array( 'type' => 'string' ),
				'excl_tax' => array( 'type' => 'string' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get bundled item output schema.
	 *
	 * @return array
	 */
	private static function get_bundled_item_output_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'bundled_item_id'                       => array( 'type' => 'integer' ),
				'product_id'                            => array( 'type' => 'integer' ),
				'product_exists'                        => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the bundled product still exists and is supported by Product Bundles.', 'woocommerce-product-bundles' ),
				),
				'product_name'                          => array( 'type' => 'string' ),
				'product_sku'                           => array( 'type' => 'string' ),
				'product_status'                        => array( 'type' => 'string' ),
				'menu_order'                            => array( 'type' => 'integer' ),
				'quantity_min'                          => array( 'type' => 'integer' ),
				'quantity_max'                          => array( 'type' => array( 'integer', 'string' ) ),
				'quantity_default'                      => array( 'type' => array( 'integer', 'string' ) ),
				'priced_individually'                   => array( 'type' => 'boolean' ),
				'shipped_individually'                  => array( 'type' => 'boolean' ),
				'override_title'                        => array( 'type' => 'boolean' ),
				'title'                                 => array( 'type' => 'string' ),
				'override_description'                  => array( 'type' => 'boolean' ),
				'description'                           => array( 'type' => 'string' ),
				'optional'                              => array( 'type' => 'boolean' ),
				'hide_thumbnail'                        => array( 'type' => 'boolean' ),
				'discount'                              => array( 'type' => 'string' ),
				'override_variations'                   => array( 'type' => 'boolean' ),
				'allowed_variations'                    => array(
					'type'  => 'array',
					'items' => array( 'type' => 'integer' ),
				),
				'override_default_variation_attributes' => array( 'type' => 'boolean' ),
				'default_variation_attributes'          => array(
					'type'        => 'array',
					'description' => __( 'Default variable-product attributes selected for this bundled item when variation defaults are overridden.', 'woocommerce-product-bundles' ),
					'items'       => array(
						'type'                 => 'object',
						'properties'           => array(
							'id'     => array(
								'type'        => 'integer',
								'description' => __( 'Attribute taxonomy ID, or 0 for custom product attributes.', 'woocommerce-product-bundles' ),
							),
							'name'   => array(
								'type'        => 'string',
								'description' => __( 'Attribute name displayed in the bundle configuration.', 'woocommerce-product-bundles' ),
							),
							'option' => array(
								'type'        => 'string',
								'description' => __( 'Selected default attribute option slug or value.', 'woocommerce-product-bundles' ),
							),
						),
						'required'             => array( 'id', 'name', 'option' ),
						'additionalProperties' => false,
					),
				),
				'single_product_visibility'             => array(
					'type' => 'string',
					'enum' => array( 'visible', 'hidden' ),
				),
				'cart_visibility'                       => array(
					'type' => 'string',
					'enum' => array( 'visible', 'hidden' ),
				),
				'order_visibility'                      => array(
					'type' => 'string',
					'enum' => array( 'visible', 'hidden' ),
				),
				'single_product_price_visibility'       => array(
					'type' => 'string',
					'enum' => array( 'visible', 'hidden' ),
				),
				'cart_price_visibility'                 => array(
					'type' => 'string',
					'enum' => array( 'visible', 'hidden' ),
				),
				'order_price_visibility'                => array(
					'type' => 'string',
					'enum' => array( 'visible', 'hidden' ),
				),
				'stock_status'                          => array(
					'type'        => 'string',
					'enum'        => array( 'in_stock', 'on_backorder', 'out_of_stock', 'unavailable' ),
					'description' => __( 'Bundled item stock availability after evaluating the child product and minimum bundled quantity.', 'woocommerce-product-bundles' ),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get product status values.
	 *
	 * @return array
	 */
	private static function get_product_status_values() {
		$statuses = get_post_stati( array(), 'names' );

		return array_values( array_unique( array_merge( array( 'publish', 'draft', 'pending', 'private' ), $statuses ) ) );
	}

	/**
	 * Get bundle stock status values.
	 *
	 * @return array
	 */
	private static function get_bundle_stock_status_values() {
		return array_values( array_unique( array_merge( array_keys( wc_get_product_stock_status_options() ), array( 'insufficientstock' ) ) ) );
	}
}
