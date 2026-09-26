<?php
/**
 * WC_PB_Coupon class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundle Coupon functions and filters.
 *
 * @class    WC_PB_Coupon
 * @version  8.5.11
 */
class WC_PB_Coupon {

	/**
	 * Whether the validity of bundled items is currently being checked.
	 *
	 * @since 8.5.11
	 *
	 * @var bool
	 */
	private static $checking_bundled_items = false;

	/**
	 * Initilize.
	 */
	public static function init() {

		// Coupons - inherit bundled item coupon validity from parent.
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( __CLASS__, 'coupon_is_valid_for_product' ), 10, 4 );

		// Coupons - a bundle container without a price of its own cannot keep a coupon valid on its own. Runs after the
		// inheritance filter above, which resolves the validity this callback then inspects.
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( __CLASS__, 'coupon_is_valid_for_bundle_container' ), 20, 4 );
	}

	/**
	 * Inherit coupon validity from parent:
	 *
	 * - Coupon is invalid for bundled item if parent is excluded.
	 * - Coupon is valid for bundled item if valid for parent, unless bundled item is excluded.
	 *
	 * @since  5.8.0
	 *
	 * @param  bool                  $valid Default validity.
	 * @param  WC_Product            $product Product object.
	 * @param  WC_Coupon             $coupon Coupon object.
	 * @param  WC_Order_Item_Product $item Order item object.
	 * @return boolean
	 */
	public static function coupon_is_valid_for_product( $valid, $product, $coupon, $item ) {

		if ( ! $coupon->is_type( wc_get_product_coupon_types() ) ) {
			return $valid;
		}

		if ( is_a( $item, 'WC_Order_Item_Product' ) ) {

			$container_item = wc_pb_get_bundled_order_item_container( $item );
			if ( $container_item ) {

				$bundle    = $container_item->get_product();
				$bundle_id = $container_item['product_id'];
			}
		} elseif ( ! empty( WC()->cart ) ) {

			$container_item = wc_pb_get_bundled_cart_item_container( $item );
			if ( $container_item ) {

				$bundle    = $container_item['data'];
				$bundle_id = $container_item['product_id'];
			}
		}

		if ( ! isset( $bundle, $bundle_id ) || empty( $container_item ) ) {
			return $valid;
		}

		/**
		 * 'woocommerce_bundles_inherit_coupon_validity' filter.
		 *
		 * Use this to prevent coupon valididty inheritance for bundled products.
		 *
		 * @since 5.8.0
		 * @param  boolean     $inherit
		 * @param  WC_Product  $product
		 * @param  WC_Coupon   $coupon
		 * @param  array       $item
		 * @param  array       $container_item
		 */
		if ( apply_filters( 'woocommerce_bundles_inherit_coupon_validity', true, $product, $coupon, $item, $container_item ) ) {
			/*
			 * If the bundled item is eligible, ensure that the container item is not excluded.
			 */
			if ( $valid ) {

				$bundle_cats = wc_get_product_cat_ids( $bundle_id );

				// Container ID excluded from the discount?
				if ( count( $coupon->get_excluded_product_ids() ) && count( array_intersect( array( $bundle_id ), $coupon->get_excluded_product_ids() ) ) ) {
					$valid = false;
				}

				// Container categories excluded from the discount?
				if ( count( $coupon->get_excluded_product_categories() ) && count( array_intersect( $bundle_cats, $coupon->get_excluded_product_categories() ) ) ) {
					$valid = false;
				}

				// Container on sale and sale items excluded from discount?
				if ( $coupon->get_exclude_sale_items() && $bundle->is_on_sale() ) {
					$valid = false;
				}

				/*
				* Otherwise, check if the bundled item is specifically excluded, and if not, consider it as eligible if its container item is eligible.
				*/
			} else {

				$product_ids      = array( $product->get_id(), $product->get_parent_id() );
				$product_cats     = wc_get_product_cat_ids( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() );
				$product_excluded = false;

				// Product IDs excluded from the discount?
				if ( count( $coupon->get_excluded_product_ids() ) && count( array_intersect( $product_ids, $coupon->get_excluded_product_ids() ) ) ) {
					$product_excluded = true;
				}

				// Product categories excluded from the discount?
				if ( count( $coupon->get_excluded_product_categories() ) && count( array_intersect( $product_cats, $coupon->get_excluded_product_categories() ) ) ) {
					$product_excluded = true;
				}

				// Product on sale and sale items excluded from discount?
				if ( $coupon->get_exclude_sale_items() && $product->is_on_sale() ) {
					$product_excluded = true;
				}

				if ( ! $product_excluded && $coupon->is_valid_for_product( $bundle, $container_item ) ) {
					$valid = true;
				}
			}
		}

		return $valid;
	}

	/**
	 * Prevent 'Exclude sale items' coupons from being accepted because of a bundle container that has no price of its own.
	 *
	 * In cart context, bundle containers are never seen as on sale - @see WC_Product_Bundle::is_on_sale, which skips the
	 * bundled-item sale/discount checks when the object context is 'cart'. A container whose entire value sits in discounted
	 * bundled items therefore passes the 'sale items' exclusion, keeping the coupon valid while every bundled item is excluded
	 * from it - the coupon is accepted and then discounts nothing.
	 *
	 * Outside cart context - order items, for example - a container is seen as on sale when it has a sale price of its own, or
	 * when it contains a discounted mandatory item. A container whose discounted items are all optional is not, so the same
	 * mismatch can occur there.
	 *
	 * Containers with a price of their own are left alone: the coupon still discounts their base price.
	 *
	 * @since 8.5.11
	 *
	 * @param  bool                        $valid Default validity.
	 * @param  WC_Product                  $product Product object.
	 * @param  WC_Coupon                   $coupon Coupon object.
	 * @param  array|WC_Order_Item_Product $item Cart item or order item object.
	 * @return bool
	 */
	public static function coupon_is_valid_for_bundle_container( $valid, $product, $coupon, $item ) {

		if ( ! $valid || self::$checking_bundled_items ) {
			return $valid;
		}

		if ( ! is_a( $coupon, 'WC_Coupon' ) || ! $coupon->is_type( wc_get_product_coupon_types() ) || ! $coupon->get_exclude_sale_items() ) {
			return $valid;
		}

		if ( ! is_a( $product, 'WC_Product' ) || ! $product->is_type( 'bundle' ) ) {
			return $valid;
		}

		if ( is_a( $item, 'WC_Order_Item_Product' ) ) {

			if ( ! wc_pb_is_bundle_container_order_item( $item ) || (float) $item->get_subtotal() > 0 ) {
				return $valid;
			}

			$bundled_items = wc_pb_get_bundled_order_items( $item );

		} elseif ( is_array( $item ) && wc_pb_is_bundle_container_cart_item( $item ) ) {

			if ( ! isset( $item['data'] ) || ! is_a( $item['data'], 'WC_Product' ) || (float) $item['data']->get_price() > 0 ) {
				return $valid;
			}

			$bundled_items = wc_pb_get_bundled_cart_items( $item );

		} else {
			return $valid;
		}

		if ( empty( $bundled_items ) ) {
			return $valid;
		}

		$has_eligible_bundled_item = false;

		self::$checking_bundled_items = true;

		try {
			foreach ( $bundled_items as $bundled_item ) {

				if ( is_a( $bundled_item, 'WC_Order_Item_Product' ) ) {
					$bundled_product = $bundled_item->get_product();
				} else {
					$bundled_product = isset( $bundled_item['data'] ) ? $bundled_item['data'] : false;
				}

				// Note: re-enters the public 'woocommerce_coupon_is_valid_for_product' filter, so third-party code can throw here.
				if ( is_a( $bundled_product, 'WC_Product' ) && $coupon->is_valid_for_product( $bundled_product, $bundled_item ) ) {
					$has_eligible_bundled_item = true;
					break;
				}
			}
		} finally {
			self::$checking_bundled_items = false;
		}

		return $has_eligible_bundled_item ? $valid : false;
	}
}

WC_PB_Coupon::init();
