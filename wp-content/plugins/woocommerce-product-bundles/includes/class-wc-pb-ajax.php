<?php
/**
 * WC_PB_Ajax class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end AJAX filters for 'get_variation'.
 *
 * @class    WC_PB_Ajax
 * @version  8.5.12
 * @since    5.0.0
 */
class WC_PB_Ajax {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Filter core 'get_variation' AJAX requests in order to account for bundled item variation filters and discounts.
		add_action( 'wc_ajax_get_variation', array( __CLASS__, 'ajax_get_bundled_variation' ), 0 );
	}

	/**
	 * Filters core 'get_variation' AJAX requests in order to account for bundled item variation filters and discounts.
	 */
	public static function ajax_get_bundled_variation() {

		//phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST['custom_data'] ) && is_array( $_POST['custom_data'] ) ) {
			$bundle_id       = isset( $_POST['custom_data']['bundle_id'] ) ? absint( $_POST['custom_data']['bundle_id'] ) : false;
			$bundled_item_id = isset( $_POST['custom_data']['bundled_item_id'] ) ? absint( $_POST['custom_data']['bundled_item_id'] ) : false;
			$product_id      = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : false;

			// Unset custom data to prevent issues in 'WC_Product_Variable::get_matching_variation'.
			unset( $_POST['custom_data'] );

			$bundle       = $bundle_id ? wc_get_product( $bundle_id ) : false;
			$bundled_item = $bundle instanceof WC_Product_Bundle ? $bundle->get_bundled_item( $bundled_item_id ) : false;

			if (
				$bundled_item
				&& ( is_post_status_viewable( $bundle->get_status() ) || current_user_can( 'edit_post', $bundle_id ) )
				&& $product_id === $bundled_item->get_product_id()
			) {
				add_filter( 'woocommerce_available_variation', array( $bundled_item, 'filter_variation' ), 10, 3 );
				$bundled_item->add_price_filters();
			}
		}
		//phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}

WC_PB_Ajax::init();
