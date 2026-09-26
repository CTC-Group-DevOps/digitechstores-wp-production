<?php
/**
 * WC_PB_Order_Again class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.8.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order-again functions and filters.
 *
 * @class    WC_PB_Order_Again
 * @version  8.5.12
 */
class WC_PB_Order_Again {

	/**
	 * Refreshed configurations, keyed by container order item ID.
	 *
	 * @var array
	 */
	private static $refreshed_bundle_configurations = array();

	/**
	 * Initilize.
	 */
	public static function init() {

		// Put back cart item data to allow re-ordering of bundles.
		add_filter( 'woocommerce_order_again_cart_item_data', array( __CLASS__, 'order_again_cart_item_data' ), 10, 3 );

		// Initialize parent-child associations from order-again keys.
		add_filter( 'woocommerce_get_cart_item_from_session', array( __CLASS__, 'get_cart_item_from_session' ), -100, 3 );

		// Finalize parent-child associations from order-again keys.
		add_action( 'woocommerce_cart_loaded_from_session', array( __CLASS__, 'cart_loaded_from_session' ), -100 );

		// Discard request-local configuration data after WooCommerce finishes rebuilding the cart.
		add_action( 'woocommerce_ordered_again', array( __CLASS__, 'clear_refreshed_bundle_configurations' ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Inialize cart item data when re-ordering.
	 * Depending on whether cart session data is loaded, a different technique is needed.
	 *
	 * @param  array               $cart_item Cart item data.
	 * @param  WC_Order_Item|array $order_item Order item data.
	 * @param  WC_Order            $order Order object.
	 * @return array
	 */
	public static function order_again_cart_item_data( $cart_item, $order_item, $order ) {

		$refresh_discounts = ! self::is_subscription_cart_item( $cart_item );

		if ( wc_pb_is_bundle_container_order_item( $order_item ) ) {

			if ( ! $order_item->meta_exists( '_stamp' ) ) {
				return $cart_item;
			}

			$cart_item['stamp']         = $order_item->get_meta( '_stamp', true );
			$cart_item['bundled_items'] = array();

			$bundle = wc_get_product( $order_item->get_product_id() );

			if ( $bundle && $bundle->is_type( 'bundle' ) ) {

				$bundled_items = $bundle->get_bundled_items();

				// If an item was optional + unselected, but no longer exists, there is no reason to include its config as it will trigger a validation error downstream.
				foreach ( $cart_item['stamp'] as $bundled_item_id => $bundled_item_configuration ) {

					if ( isset( $bundled_item_configuration['optional_selected'] ) && 'no' === $bundled_item_configuration['optional_selected'] ) {

						if ( ! $bundle->has_bundled_item( $bundled_item_id ) ) {
							unset( $cart_item['stamp'][ $bundled_item_id ] );
						}
					}
				}

				// If an item was not optional, but became later, include the 'optional_selected' variable in its config.
				foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {

					if ( ! isset( $cart_item['stamp'][ $bundled_item_id ] ) ) {
						continue;
					}

					$bundled_item_configuration = $cart_item['stamp'][ $bundled_item_id ];

					if ( $bundled_item->is_optional() && ! isset( $bundled_item_configuration['optional_selected'] ) ) {
						$cart_item['stamp'][ $bundled_item_id ]['optional_selected'] = isset( $bundled_item_configuration['quantity'] ) && absint( $bundled_item_configuration['quantity'] ) > 0 ? 'yes' : 'no';
					}
				}

				if ( $refresh_discounts ) {
					$cart_item['stamp'] = self::refresh_bundle_configuration( $bundle, $cart_item['stamp'], $order_item );
				}
			}

			if ( WC_PB()->cart->is_cart_session_loaded() ) {

				// If this is part of a Composite, the Composite will add the Bundle to the cart.
				if ( WC_PB()->compatibility->is_composited_order_item( $order_item, $order ) ) {
					return $cart_item;
				}
			} else {

				$cart_id = $order_item->get_meta( '_bundle_cart_key', true );

				if ( ! empty( $cart_id ) ) {
					$cart_item['order_again_bundle_cart_key'] = $cart_id;
				}
			}
		} elseif ( wc_pb_is_bundled_order_item( $order_item, $order ) ) {

			$bundled_item_id = $order_item->get_meta( '_bundled_item_id', true );

			if ( WC_PB()->cart->is_cart_session_loaded() ) {

				if ( $bundled_item_id ) {

					$modified_cart = false;

					// Copy all cart data of the "orphaned" bundled cart item into the one already added by the container on 'woocommerce_add_to_cart'.
					foreach ( WC()->cart->cart_contents as $check_cart_item_key => $check_cart_item_data ) {

						if ( empty( $check_cart_item_data['bundled_item_id'] ) ) {
							continue;
						}

						if ( absint( $bundled_item_id ) !== absint( $check_cart_item_data['bundled_item_id'] ) ) {
							continue;
						}

						$existing_bundled_cart_item     = $check_cart_item_data;
						$existing_bundled_cart_item_key = $check_cart_item_key;

						foreach ( $cart_item as $key => $value ) {
							if ( ! isset( $existing_bundled_cart_item[ $key ] ) ) {
								WC()->cart->cart_contents[ $existing_bundled_cart_item_key ][ $key ] = $value;
								$modified_cart = true;
							}
						}
					}

					// Cart data changed? Recalculate totals and set session.
					if ( $modified_cart ) {
						WC()->cart->calculate_totals();
					}
				}

				// Identify this as a cart item that is originally part of a bundle. Will be removed since it has already been added to the cart by its container.
				$cart_item['is_order_again_bundled'] = 'yes';

			} else {

				if ( $bundled_item_id ) {
					$cart_item['bundled_item_id'] = $bundled_item_id;
				}

				$cart_id       = $order_item->get_meta( '_bundle_cart_key', true );
				$bundled_by    = $order_item->get_meta( '_bundled_by', true );
				$configuration = $order_item->get_meta( '_stamp', true );

				if ( ! empty( $cart_id ) ) {
					$cart_item['order_again_bundle_cart_key'] = $cart_id;
				}

				if ( ! empty( $bundled_by ) ) {
					$cart_item['order_again_bundled_by'] = $bundled_by;
				}

				if ( ! empty( $configuration ) ) {
					/*
					 * In the standard front-end flow this branch runs before the cart session is loaded, which means each bundled order item becomes a real cart item that is priced from this copy of the configuration.
					 * Refresh its discounts from the same source used by the container branch, so both end up carrying identical, up-to-date data.
					 */
					if ( $refresh_discounts ) {

						$container_order_item = wc_pb_get_bundled_order_item_container( $order_item, $order );
						$container_bundle     = $container_order_item && is_callable( array( $container_order_item, 'get_product_id' ) ) ? wc_get_product( $container_order_item->get_product_id() ) : false;

						if ( is_a( $container_bundle, 'WC_Product_Bundle' ) ) {
							$configuration = self::refresh_bundle_configuration( $container_bundle, $configuration, $container_order_item );
						}
					}

					$cart_item['stamp'] = $configuration;
				}
			}
		}

		return $cart_item;
	}

	/**
	 * Refreshes a retained configuration once for each bundle container in an Order Again request.
	 *
	 * @param  WC_Product_Bundle $bundle               Bundle product.
	 * @param  array             $configuration        Retained configuration.
	 * @param  WC_Order_Item     $container_order_item Container order item.
	 * @return array
	 */
	private static function refresh_bundle_configuration( $bundle, $configuration, $container_order_item ) {

		$container_item_id = is_callable( array( $container_order_item, 'get_id' ) ) ? absint( $container_order_item->get_id() ) : 0;

		if ( ! $container_item_id ) {
			return WC_PB()->cart->refresh_bundle_configuration( $bundle, $configuration );
		}

		if ( ! isset( self::$refreshed_bundle_configurations[ $container_item_id ] ) ) {
			self::$refreshed_bundle_configurations[ $container_item_id ] = WC_PB()->cart->refresh_bundle_configuration( $bundle, $configuration );
		}

		return self::$refreshed_bundle_configurations[ $container_item_id ];
	}

	/**
	 * Clears request-local refreshed configurations.
	 */
	public static function clear_refreshed_bundle_configurations() {
		self::$refreshed_bundle_configurations = array();
	}

	/**
	 * Whether a cart item is built by WooCommerce Subscriptions to pay for an existing order.
	 *
	 * Initial payment, resubscribe and renewal carts must be priced exactly as recorded on the order they are paying for, so their configuration must be left untouched.
	 * The keys checked here are only ever added by Subscriptions, which makes this inert when Subscriptions is not installed.
	 *
	 * @since 8.5.12
	 *
	 * @param  array $cart_item Cart item data.
	 * @return boolean
	 */
	private static function is_subscription_cart_item( $cart_item ) {

		if ( ! is_array( $cart_item ) ) {
			return false;
		}

		return isset( $cart_item['subscription_renewal'] ) || isset( $cart_item['subscription_initial_payment'] ) || isset( $cart_item['subscription_resubscribe'] );
	}

	/**
	 * Initialize parent-child associations from order-again keys.
	 *
	 * @param  array $cart_item Cart item data.
	 * @param  array $cart_session_item Cart session item data.
	 * @param  array $cart_item_key Cart item key.
	 * @return array
	 */
	public static function get_cart_item_from_session( $cart_item, $cart_session_item, $cart_item_key ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		if ( ! did_action( 'woocommerce_ordered_again' ) ) {
			return $cart_item;
		}

		if ( ! empty( $cart_item['order_again_bundled_by'] ) ) {

			if ( empty( $cart_session_item['order_again_bundle_cart_key'] ) ) {
				return $cart_item;
			}

			foreach ( WC()->cart->cart_contents as $search_container_item_key => $search_container_item ) {

				if ( empty( $search_container_item['order_again_bundle_cart_key'] ) ) {
					continue;
				}

				if ( $cart_session_item['order_again_bundled_by'] === $search_container_item['order_again_bundle_cart_key'] ) {
					// Add reference to parent key in child.
					$cart_item['bundled_by'] = $search_container_item_key;
					// Break the search.
					break;
				}
			}

			// Clean up.
			unset( $cart_item['order_again_bundle_cart_key'] );
			unset( $cart_item['order_again_bundled_by'] );
		}

		return $cart_item;
	}

	/**
	 * Finalize parent-child associations from order-again keys.
	 *
	 * @param  WC_Cart $cart Cart object.
	 * @return void
	 */
	public static function cart_loaded_from_session( $cart ) {

		if ( ! did_action( 'woocommerce_ordered_again' ) ) {
			return;
		}

		if ( empty( $cart->cart_contents ) ) {
			return;
		}

		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( wc_pb_is_bundle_container_cart_item( $cart_item ) ) {

				foreach ( $cart->cart_contents as $search_child_key => $search_child_item ) {

					if ( ! wc_pb_maybe_is_bundled_cart_item( $search_child_item ) ) {
						continue;
					}

					if ( $search_child_item['bundled_by'] === $cart_item_key ) {

						// Add reference to child key in parent item.
						WC()->cart->cart_contents[ $cart_item_key ]['bundled_items'][] = $search_child_key;
						// Invalidate session data.
						WC()->session->set( 'cart_totals', null );
					}
				}

				// Clean up.
				unset( WC()->cart->cart_contents[ $cart_item_key ]['order_again_bundle_cart_key'] );
			}
		}
	}
}

WC_PB_Order_Again::init();
