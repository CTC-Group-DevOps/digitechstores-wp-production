<?php

namespace WCML\EditorScopedSync;

use WCML\Synchronization\Manager as SyncManager;
use function WPML\Container\make;

class ForceUpdateEndpoint implements \IWPML_Backend_Action {

	const ACTION = 'wcml_force_full_variation_sync';
	const NONCE  = 'wcml_force_full_variation_sync';

	const PRIORITY_OVERRIDE_SYNC_GATE = 100;

	public function add_hooks() {
		\WCML\Request\Ajax::register( self::ACTION, [ 'capability' => 'edit_products', 'nonce' => [ self::NONCE, '_wpnonce' ] ], [ __CLASS__, 'handle' ] );
	}

	public static function handle() {
		check_ajax_referer( self::NONCE, '_wpnonce' );
		if ( ! current_user_can( 'edit_products' ) ) {
			wp_send_json_error( [ 'message' => 'forbidden' ], 403 );
		}

		$productId = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		if ( ! $productId ) {
			wp_send_json_error( [ 'message' => 'missing product_id' ], 400 );
		}

		$product = get_post( $productId );
		if ( ! $product ||  'product' !== $product->post_type) {
			wp_send_json_error( [ 'message' => 'invalid product' ], 404 );
		}

		if ( ! current_user_can( 'edit_post', $productId ) ) {
			wp_send_json_error( [ 'message' => 'forbidden' ], 403 );
		}

		add_filter( 'wcml_editor_scoped_variation_ids', '__return_null', self::PRIORITY_OVERRIDE_SYNC_GATE );

		try {
			do_action( 'wcml_force_full_product_sync_run', $productId, $product );
			make( SyncManager::class )->run( $product );
			$message = 'sync completed';
			$success = true;
		} catch ( \Throwable $e ) {
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				error_log( sprintf( 'WCML force variation sync failed for product %d: %s', $productId, $e->getMessage() ) );
			}
			$message = 'error: sync failed';
			$success = false;
		}

		remove_filter( 'wcml_editor_scoped_variation_ids', '__return_null', self::PRIORITY_OVERRIDE_SYNC_GATE );

		wp_send_json(
			[
				'product_id' => $productId,
				'message'    => $message,
				'success'    => $success,
			],
			200
		);
	}

	public static function nonce() {
		return wp_create_nonce( self::NONCE );
	}
}
