<?php
/**
 * WC_PB_Bundled_Item_Formatters class
 *
 * @package WooCommerce Product Bundles
 * @since   8.5.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared bundled item formatting helpers.
 *
 * @class   WC_PB_Bundled_Item_Formatters
 * @version 8.5.9
 */
class WC_PB_Bundled_Item_Formatters {

	/**
	 * Get default bundled variable product attributes - @see 'WC_REST_Products_Controller::get_default_attributes'.
	 *
	 * @param  WC_Bundled_Item_Data $bundled_item_data Bundled item data.
	 * @version 8.5.9
	 * @return array
	 */
	public static function get_bundled_item_attribute_defaults( $bundled_item_data ) {
		$default = array();
		$product = wc_get_product( $bundled_item_data->get_product_id() );

		if ( $product && $product->is_type( 'variable' ) ) {
			foreach ( array_filter( (array) $bundled_item_data->get_meta( 'default_variation_attributes' ), 'strlen' ) as $key => $value ) {
				$default[] = array(
					'id'     => 0 === strpos( $key, 'pa_' ) ? wc_attribute_taxonomy_id_by_name( $key ) : 0,
					'name'   => self::get_attribute_taxonomy_name( $key, $product ),
					'option' => $value,
				);
			}
		}

		return $default;
	}

	/**
	 * Get bundled item stock status, taking min quantity into account.
	 *
	 * @param  WC_Bundled_Item_Data $bundled_item_data Bundled item data.
	 * @param  WC_Product_Bundle    $bundle            Bundle product object.
	 * @param  string               $unavailable       Status to use when the bundled product is unavailable.
	 * @version 8.5.9
	 * @return string
	 */
	public static function get_bundled_item_stock_status( $bundled_item_data, $bundle, $unavailable = 'in_stock' ) {
		$bundled_item = wc_pb_get_bundled_item( $bundled_item_data, $bundle );

		if ( ! $bundled_item ) {
			return $unavailable;
		}

		return $bundled_item->get_stock_status();
	}

	/**
	 * Get product attribute taxonomy name - @see 'WC_REST_Products_Controller::get_attribute_taxonomy_name'.
	 *
	 * @param  string     $slug    Attribute slug.
	 * @param  WC_Product $product Product object.
	 * @version 8.5.9
	 * @return string
	 */
	private static function get_attribute_taxonomy_name( $slug, $product ) {
		$attributes = $product->get_attributes();

		if ( ! isset( $attributes[ $slug ] ) ) {
			return str_replace( 'pa_', '', $slug );
		}

		$attribute = $attributes[ $slug ];

		if ( $attribute->is_taxonomy() ) {
			$taxonomy = $attribute->get_taxonomy_object();
			return $taxonomy->attribute_label;
		}

		return $attribute->get_name();
	}
}
