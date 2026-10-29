<?php
/**
 * WC_PB_Admin_Analytics_Sync class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\Cache as ReportsCache;
use Automattic\WooCommerce\Admin\API\Reports\Products\DataStore as ProductsDataStore;

/**
 * Admin Reports Class for syncing the lookup table.
 *
 * @class    WC_PB_Admin_Analytics_Sync
 * @version  8.5.16
 */
class WC_PB_Admin_Analytics_Sync {

	/**
	 * Keeps track of which order ID is being processed.
	 *
	 * @var int|false
	 */
	protected static $updating_order_id = false;

	/**
	 * The last item in an order is the one to trigger the aggregation process.
	 *
	 * @var int|false
	 */
	protected static $trigger_order_item_id = false;

	/**
	 * Keeps track of which bundled order items need to be aggregated into their container.
	 *
	 * @var array
	 */
	protected static $bundled_order_item_ids = array();

	/**
	 * Setup Admin class.
	 *
	 * @return void
	 */
	public static function init() {
		/*
		 * Update order stats to merge bundled item stats into the container item.
		 */
		add_action( 'woocommerce_analytics_update_product', array( __CLASS__, 'update_order_item_stats' ), 10, 2 );

		/*
		 * Clean bundles data when an order is deleted.
		 */
		add_action( 'woocommerce_analytics_delete_order_stats', array( __CLASS__, 'sync_on_order_delete' ), 10 );

		/*
		 * Cache invalidation for PB reports.
		 */
		add_action( 'woocommerce_product_object_updated_props', array( __CLASS__, 'sync_product_updated_props' ), 10, 2 );

		if ( is_admin() ) {

			// Add status tool to regenerate order stats table.
			add_filter( 'woocommerce_debug_tools', array( __CLASS__, 'add_regenerate_order_item_stats_tool' ) );

			// Handle regenerate button clicks.
			add_action( 'admin_init', array( __CLASS__, 'handle_trigger_order_item_stats_update' ) );
		}

		// AS action for regenerating order stats for a batch of orders.
		add_action( 'wc_pb_process_order_item_stats_update_batch', array( __CLASS__, 'process_order_item_stats_update_batch' ), 10, 2 );
	}

	/**
	 * Update order stats if triggered.
	 *
	 * @return void
	 */
	public static function handle_trigger_order_item_stats_update() {

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		if ( ! empty( $_GET['trigger_wc_pb_order_item_stats_db_update'] ) && isset( $_GET['_wc_pb_admin_nonce'] ) && wp_verify_nonce( wc_clean( $_GET['_wc_pb_admin_nonce'] ), 'wc_pb_trigger_order_item_stats_db_update_nonce' ) && ! self::is_order_item_stats_update_queued() ) {
			self::queue_order_item_stats_update();
			wp_safe_redirect( remove_query_arg( array( 'trigger_wc_pb_order_item_stats_db_update', '_wc_pb_admin_nonce' ) ) );
			exit;
		}
	}

	/**
	 * Number of orders per regeneration batch.
	 *
	 * @return int
	 */
	protected static function get_batch_size() {
		return 20;
	}

	/**
	 * Updates all order stats table rows that involve gift cards.
	 *
	 * @return string
	 */
	public static function queue_order_item_stats_update() {

		global $wpdb;

		$order_item_stats_table_name = ProductsDataStore::get_db_table_name();
		$pb_bundled_items_table_name = $wpdb->prefix . 'woocommerce_bundled_items';

		// Count distinct orders in order product stats lookup table that also exist in PB items table.
		// Disable PHPCS sniffs as it is not possible to use prepare for database names.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$count = $wpdb->get_var(
			"
			SELECT COUNT( DISTINCT `stats`.`order_id` )
			FROM `{$order_item_stats_table_name}` AS `stats`
			INNER JOIN `{$pb_bundled_items_table_name}` AS `bundled_items`
			ON `stats`.`product_id` = `bundled_items`.`bundle_id`
		"
		);
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( ! $count ) {
			return __( 'Tool ran. No orders found to process.', 'woocommerce-product-bundles' );
		}

		$batches = ceil( $count / self::get_batch_size() );

		// Cancel existing jobs and restart.
		WC()->queue()->cancel_all( 'wc_pb_process_order_item_stats_update_batch' );

		// Queue first batch.
		self::queue_order_item_stats_update_batch( 1, $batches );

		if ( ! class_exists( 'WC_PB_Admin_Notices' ) ) {
			require_once WC_PB_ABSPATH . 'includes/admin/class-wc-pb-admin-notices.php';
		}

		WC_PB_Admin_Notices::add_maintenance_notice( 'update_order_item_stats' );

		return __( 'Tool ran.', 'woocommerce-product-bundles' );
	}

	/**
	 * Schedules a batch of row renenerations for order stats.
	 *
	 * @param  int       $batch   Batch number.
	 * @param  int|float $batches Total number of batches.
	 * @return void
	 */
	protected static function queue_order_item_stats_update_batch( $batch, $batches ) {
		WC()->queue()->add( 'wc_pb_process_order_item_stats_update_batch', array( $batch, $batches ), 'wc_pb_regenerate_order_item_stats' );
	}

	/**
	 * Indicates whether an order stats regeneration has been attempted in the past.
	 *
	 * @return boolean
	 */
	public static function is_order_item_stats_update_actioned() {

		$order_item_stats_update_batches = WC()->queue()->search(
			array(
				'hook' => 'wc_pb_process_order_item_stats_update_batch',
			)
		);

		return ! empty( $order_item_stats_update_batches );
	}

	/**
	 * Indicates whether an order stats regeneration is currently queued.
	 *
	 * @return boolean
	 */
	public static function is_order_item_stats_update_queued() {

		$order_item_stats_update_batches = WC()->queue()->search(
			array(
				'hook'   => 'wc_pb_process_order_item_stats_update_batch',
				'status' => ActionScheduler_Store::STATUS_PENDING,
			)
		);

		return ! empty( $order_item_stats_update_batches );
	}

	/**
	 * Prepares the controller logic for updating order item stats data.
	 *
	 * @param  WC_Order $order Order object.
	 * @return void
	 */
	protected static function prepare_order_item_stats_update( $order ) {

		self::$updating_order_id = $order->get_id();
		$search_order_items      = $order->get_items();
		$search_order_item_ids   = array_keys( $search_order_items );

		foreach ( $search_order_items as $search_order_item_id => $search_order_item ) {

			$bundled_order_item_ids = wc_pb_get_bundled_order_items( $search_order_item, $order, true );
			if ( $bundled_order_item_ids ) {
				self::$bundled_order_item_ids[ $search_order_item_id ] = $bundled_order_item_ids;
			}

			if ( end( $search_order_item_ids ) === $search_order_item_id ) {
				self::$trigger_order_item_id = $search_order_item_id;
			}
		}
	}

	/**
	 * Updates the order item stats table for a single item.
	 *
	 * @return void
	 */
	protected static function process_order_item_stats_update() {

		global $wpdb;

		// Nothing to process? Release the order so the next one is not mistaken for a stale update.
		if ( false === self::$updating_order_id || empty( self::$bundled_order_item_ids ) ) {
			self::reset_runtime_variables();
			return;
		}

		$pb_table_name = WC_PB_Analytics_Revenue_Data_Store::get_db_table_name();
		$wc_table_name = ProductsDataStore::get_db_table_name();

		// Get existing items in core lookup table.
		$results = $wpdb->get_results(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT * FROM `{$wc_table_name}` WHERE `order_id` = %d",
				self::$updating_order_id
			)
		);

		$order_item_stats = array();
		foreach ( $results as $item_results ) {
			$order_item_stats[ $item_results->order_item_id ] = array_diff_key(
				(array) $item_results,
				array(
					'shipping_amount'     => 1,
					'shipping_tax_amount' => 1,
				)
			);
		}

		// Delete existing items in our lookup table.
		$wpdb->query(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"DELETE FROM `{$pb_table_name}` WHERE `order_id` = %d",
				self::$updating_order_id
			)
		);

		foreach ( self::$bundled_order_item_ids as $container_order_item_id => $bundled_order_item_ids ) {

			if ( ! isset( $order_item_stats[ $container_order_item_id ] ) ) {
				continue;
			}

			$item_stats           = $order_item_stats[ $container_order_item_id ];
			$bundle_id            = $item_stats['product_id'];
			$bundle_order_item_id = $item_stats['order_item_id'];

			// Write container item stats.
			$wpdb->replace(
				$pb_table_name,
				array(
					'order_item_id'         => $item_stats['order_item_id'],
					'parent_order_item_id'  => 0,
					'order_id'              => $item_stats['order_id'],
					'bundle_id'             => $bundle_id,
					'product_id'            => $item_stats['product_id'],
					'variation_id'          => $item_stats['variation_id'],
					'customer_id'           => $item_stats['customer_id'],
					'product_qty'           => $item_stats['product_qty'],
					'product_net_revenue'   => $item_stats['product_net_revenue'],
					'date_created'          => $item_stats['date_created'],
					'coupon_amount'         => $item_stats['coupon_amount'],
					'tax_amount'            => $item_stats['tax_amount'],
					'product_gross_revenue' => $item_stats['product_gross_revenue'],
				),
				array(
					'%d', // order_item_id.
					'%d', // parent_order_item_id.
					'%d', // order_id.
					'%d', // bundle_id.
					'%d', // product_id.
					'%d', // variation_id.
					'%d', // customer_id.
					'%d', // product_qty.
					'%f', // product_net_revenue.
					'%s', // date_created.
					'%f', // coupon_amount.
					'%f', // tax_amount.
					'%f', // product_gross_revenue.
				)
			); // WPCS: cache ok, DB call ok, unprepared SQL ok.

			foreach ( $bundled_order_item_ids as $bundled_order_item_id ) {

				if ( ! isset( $order_item_stats[ $bundled_order_item_id ] ) ) {
					continue;
				}

				$item_stats = $order_item_stats[ $bundled_order_item_id ];

				// Write bundled item stats.
				$wpdb->replace(
					$pb_table_name,
					array(
						'order_item_id'         => $item_stats['order_item_id'],
						'parent_order_item_id'  => $bundle_order_item_id,
						'order_id'              => $item_stats['order_id'],
						'bundle_id'             => $bundle_id,
						'product_id'            => $item_stats['product_id'],
						'variation_id'          => $item_stats['variation_id'],
						'customer_id'           => $item_stats['customer_id'],
						'product_qty'           => $item_stats['product_qty'],
						'product_net_revenue'   => $item_stats['product_net_revenue'],
						'date_created'          => $item_stats['date_created'],
						'coupon_amount'         => $item_stats['coupon_amount'],
						'tax_amount'            => $item_stats['tax_amount'],
						'product_gross_revenue' => $item_stats['product_gross_revenue'],
					),
					array(
						'%d', // order_item_id.
						'%d', // parent_order_item_id.
						'%d', // order_id.
						'%d', // bundle_id.
						'%d', // product_id.
						'%d', // variation_id.
						'%d', // customer_id.
						'%d', // product_qty.
						'%f', // product_net_revenue.
						'%s', // date_created.
						'%f', // coupon_amount.
						'%f', // tax_amount.
						'%f', // product_gross_revenue.
					)
				); // WPCS: cache ok, DB call ok, unprepared SQL ok.
			}
		}

		// Reset.
		self::reset_runtime_variables();
	}

	/**
	 * Resets shared variables in context.
	 *
	 * @return void
	 */
	protected static function reset_runtime_variables() {
		self::$updating_order_id      = false;
		self::$trigger_order_item_id  = false;
		self::$bundled_order_item_ids = array();
	}

	/*
	|--------------------------------------------------------------------------
	| Callbacks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Regenerates order stats for single batch of orders.
	 *
	 * @param  int $batch  Batch number.
	 * @param  int $batches Total number of batches.
	 * @return void
	 */
	public static function process_order_item_stats_update_batch( $batch, $batches ) {

		global $wpdb;

		// Retry a lookup key upgrade that the database update gave up on, so refunds can be regenerated too.
		if ( 1 === absint( $batch ) ) {
			WC_PB_Install::upgrade_refund_analytics_schema();
		}

		$order_item_stats_table_name = ProductsDataStore::get_db_table_name();
		$pb_bundled_items_table_name = $wpdb->prefix . 'woocommerce_bundled_items';

		// Find order IDs to process.
		// Disable PHPCS sniffs as it is not possible to use prepare for database names.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT DISTINCT `order_id`
			FROM `{$order_item_stats_table_name}` AS `stats`
			INNER JOIN `{$pb_bundled_items_table_name}` AS `bundled_items`
			ON `stats`.`product_id` = `bundled_items`.`bundle_id`
			ORDER BY `order_id`
			LIMIT %d
			OFFSET %d
		",
				self::get_batch_size(),
				( $batch - 1 ) * self::get_batch_size()
			),
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( empty( $results ) ) {
			return;
		}

		$order_ids = wp_list_pluck( $results, 'order_id' );

		// Do the work.
		foreach ( $order_ids as $order_id ) {
			ProductsDataStore::sync_order_products( $order_id );

			// Refunds of bundled items alone have no bundle row of their own, so the query above cannot find them.
			$order = wc_get_order( $order_id );
			if ( $order instanceof WC_Order ) {
				foreach ( $order->get_refunds() as $refund ) {
					ProductsDataStore::sync_order_products( $refund->get_id() );
				}
			}
		}

		// Queue next batch.
		if ( $batch < $batches ) {
			self::queue_order_item_stats_update_batch( $batch + 1, $batches );
		}

		// Invalidate report's cache.
		Automattic\WooCommerce\Admin\API\Reports\Cache::invalidate();
	}

	/**
	 * Updates the order item stats table for a single item.
	 *
	 * @param  int $order_item_id Order item ID.
	 * @param  int $order_id    Order ID.
	 * @return void
	 */
	public static function update_order_item_stats( $order_item_id, $order_id ) {

		// Find items to process.
		if ( false === self::$updating_order_id ) {

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}

			if ( 'shop_order_refund' === $order->get_type() ) {
				self::sync_refund_order_item_stats( $order, $order_item_id );
				return;
			}

			if ( 'shop_order' !== $order->get_type() ) {
				return;
			}

			self::prepare_order_item_stats_update( $order );

			// Currently working for updating_order_id...
		} else {

			// This should never happen.
			if ( self::$updating_order_id !== $order_id ) {
				self::reset_runtime_variables();
				return;
			}

			// Nothing to process?
			if ( empty( self::$bundled_order_item_ids ) ) {
				self::reset_runtime_variables();
				return;
			}

			// This should never happen, either.
			if ( empty( self::$trigger_order_item_id ) ) {
				self::reset_runtime_variables();
				return;
			}

			// Data has been collected, but we are not at the end yet.
			if ( $order_item_id !== self::$trigger_order_item_id ) {
				return;
			}
		}

		// Do the work!
		if ( $order_item_id === self::$trigger_order_item_id ) {
			self::process_order_item_stats_update();
		}
	}

	/**
	 * Copy native refund metrics using the original order's container membership.
	 *
	 * @since 8.5.16
	 * @param WC_Order_Refund $refund Refund order.
	 * @param int             $order_item_id Native item ID, or zero to synchronize all refund rows.
	 * @return bool Whether the lookup writes succeeded.
	 */
	public static function sync_refund_order_item_stats( $refund, $order_item_id = 0 ) {
		global $wpdb;

		if ( ! $refund instanceof WC_Order_Refund || '1' !== get_option( 'wc_pb_refund_analytics_schema' ) ) {
			return true;
		}

		// A non-persistent, site-scoped cache keeps only the current refund's membership.
		$cache_group = 'wc_pb_refund_analytics';
		wp_cache_add_non_persistent_groups( $cache_group );
		$context = wp_cache_get( 'current', $cache_group );
		// Core reports each refund item once per sync, so a repeated item starts a new sync.
		if ( ! $order_item_id || ! is_array( $context ) || $context['refund_id'] !== $refund->get_id() || isset( $context['synced'][ $order_item_id ] ) ) {
			$membership     = array();
			$refunded_items = array();
			$parent         = wc_get_order( $refund->get_parent_id() );
			if ( $parent && ! is_wp_error( $parent ) ) {
				foreach ( $parent->get_items() as $container_id => $container ) {
					if ( ! is_object( $container ) || ! method_exists( $container, 'get_product_id' ) ) {
						continue;
					}
					$children = wc_pb_get_bundled_order_items( $container, $parent, true );
					if ( ! $children ) {
						continue;
					}
					$membership[ $container_id ] = array( 0, $container->get_product_id( 'edit' ) );
					foreach ( $children as $child_id ) {
						$membership[ $child_id ] = array( $container_id, $container->get_product_id( 'edit' ) );
					}
				}
			}
			foreach ( $refund->get_items() as $item_id => $item ) {
				$original_id                = $item->get_meta( '_refunded_item_id' );
				$refunded_items[ $item_id ] = is_numeric( $original_id ) ? absint( $original_id ) : 0;
			}
			$context = array(
				'refund_id'      => $refund->get_id(),
				'membership'     => $membership,
				'refunded_items' => $refunded_items,
				'synced'         => array(),
			);
			// Rebuild the refund's rows, like an order's, so rows that native Analytics or the bundle membership no longer has do not linger.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- This class owns the derived lookup writes.
			if ( false === $wpdb->delete( $wpdb->prefix . 'wc_order_bundle_lookup', array( 'order_id' => $refund->get_id() ), array( '%d' ) ) ) {
				WC_PB_Core_Compatibility::log( $wpdb->last_error, 'error', 'wc_pb_db_updates' );
				return false;
			}
		}
		if ( $order_item_id ) {
			$context['synced'][ $order_item_id ] = true;
			wp_cache_set( 'current', $context, $cache_group );
		} else {
			wp_cache_delete( 'current', $cache_group );
		}
		$membership     = $context['membership'];
		$refunded_items = $context['refunded_items'];
		if ( ! $membership ) {
			return true;
		}

		$table      = $wpdb->prefix . 'wc_order_bundle_lookup';
		$core_table = ProductsDataStore::get_db_table_name();
		$item_where = $order_item_id ? $wpdb->prepare( ' AND order_item_id = %d', $order_item_id ) : '';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Synchronize the just-written native lookup, not a cached report.
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$core_table} WHERE order_id = %d", $refund->get_id() ) . $item_where, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
		if ( $wpdb->last_error ) {
			WC_PB_Core_Compatibility::log( $wpdb->last_error, 'error', 'wc_pb_db_updates' );
			return false;
		}
		$columns = array_flip( array( 'order_item_id', 'order_id', 'product_id', 'variation_id', 'customer_id', 'product_qty', 'product_net_revenue', 'date_created', 'coupon_amount', 'tax_amount', 'product_gross_revenue' ) );
		foreach ( $rows as $row ) {
			$original_id = $refunded_items[ $row['order_item_id'] ] ?? (int) $row['order_item_id'];
			if ( ! isset( $membership[ $original_id ] ) ) {
				continue;
			}
			$row                         = array_intersect_key( $row, $columns );
			$row['order_item_id']        = $original_id;
			$row['parent_order_item_id'] = $membership[ $original_id ][0];
			$row['bundle_id']            = $membership[ $original_id ][1];
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- This class owns the derived lookup writes.
			if ( false === $wpdb->replace( $table, $row ) ) {
				WC_PB_Core_Compatibility::log( $wpdb->last_error, 'error', 'wc_pb_db_updates' );
				return false;
			}
		}
		return true;
	}

	/**
	 * Clean bundles data when an order is deleted.
	 *
	 * @param int $order_id Order ID.
	 * @return void
	 */
	public static function sync_on_order_delete( $order_id ) {
		global $wpdb;

		$pb_table_name = WC_PB_Analytics_Revenue_Data_Store::get_db_table_name();
		$wpdb->delete( $pb_table_name, array( 'order_id' => $order_id ) );

		/**
		 * Fires when product's reports are removed from database.
		 *
		 * @since 6.9.0
		 * @param int $product_id Product ID.
		 * @param int $order_id   Order ID.
		 */
		do_action( 'woocommerce_analytics_delete_bundle', 0, $order_id );

		ReportsCache::invalidate();
	}

	/**
	 * Adds status tool to regenerate the order stats table.
	 *
	 * @param  array $tools Debug tools.
	 * @return array
	 */
	public static function add_regenerate_order_item_stats_tool( $tools ) {

		if ( WC_PB_Core_Compatibility::is_wc_admin_active() ) {

			$tools['pb_regenerate_order_item_stats'] = array(
				'name'     => __( 'Regenerate Product Bundles revenue analytics data', 'woocommerce-product-bundles' ),
				'button'   => __( 'Regenerate data', 'woocommerce-product-bundles' ),
				'desc'     => sprintf( __( 'Regenerates historical Revenue data under <strong>Analytics > Bundles</strong>.', 'woocommerce-product-bundles' ), WC_PB()->get_resource_url( 'analytics-revenue' ) ),
				'callback' => array( __CLASS__, 'queue_order_item_stats_update' ),
			);
		}

		return $tools;
	}

	/**
	 * Handle stock report's cache invalidation.
	 *
	 * @param  WC_Product $product       Product object.
	 * @param  array      $updated_props Updated properties.
	 * @return void
	 */
	public static function sync_product_updated_props( $product, $updated_props ) {

		$props_to_check = array(
			'bundled_items_stock_status',
			'bundle_stock_quantity',
			'stock_quantity',
			'stock_status',
		);

		if ( ! empty( array_intersect( $props_to_check, $updated_props ) ) ) {

			WC_Cache_Helper::get_transient_version( 'woocommerce_bundles_stock_reports', true );

			if ( $product->is_type( 'bundle' ) ) {
				WC_Cache_Helper::get_transient_version( 'woocommerce_bundles_revenue_reports', true );
			}
		}
	}
}
