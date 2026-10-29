<?php

namespace WCML\Synchronization\Component;

class Stock extends SynchronizerForMeta {

	const STOCK_META_KEY        = '_stock';
	const STOCK_STATUS_META_KEY = '_stock_status';

	private static bool $deferCacheDeletes = false;

	private static array $pendingCacheDeletes = [];

	private static bool $pendingCountTransientsDelete = false;

	public function run( $product, $translationsIds, $translationsLanguages ) {
		$productId              = (int) $product->ID;
		$productObject          = wc_get_product( $productId );
		$productIdManagingStock = $productObject->get_stock_managed_by_id();

		$translationsManagingStock = $translationsIds;
		if ( $productIdManagingStock !== $productId ) {
			$translationsManagingStock = $this->elementTranslations->get_element_translations( $productIdManagingStock, false, false );
		}

		$writtenIds = array_merge(
			$this->synchronizeMeta( $productIdManagingStock, $translationsManagingStock, self::STOCK_META_KEY ),
			$this->synchronizeMeta( $productId, $translationsIds, self::STOCK_STATUS_META_KEY )
		);
		foreach ( array_intersect( $writtenIds, $translationsIds ) as $writtenId ) {
			$this->syncHashManager->markChanged( $writtenId );
		}

		if ( $productIdManagingStock !== $productId ) {
			$this->deleteCache( $productIdManagingStock );
			foreach ( $translationsManagingStock as $translationManagingStock ) {
				$this->deleteCache( $translationManagingStock );
			}
		}

		if ( self::$deferCacheDeletes ) {
			self::$pendingCountTransientsDelete = true;
		} else {
			self::deleteCountTransients();
		}
	}

	public static function deferCacheDeletes() {
		self::$deferCacheDeletes = true;
	}

	public static function flushCacheDeletes() {
		self::$deferCacheDeletes = false;

		$productIds                = array_keys( self::$pendingCacheDeletes );
		self::$pendingCacheDeletes = [];
		foreach ( $productIds as $productId ) {
			self::deleteCacheNow( $productId );
		}

		if ( self::$pendingCountTransientsDelete ) {
			self::$pendingCountTransientsDelete = false;
			self::deleteCountTransients();
		}
	}

	private function deleteCache( $productId ) {
		if ( self::$deferCacheDeletes ) {
			self::$pendingCacheDeletes[ (int) $productId ] = true;
			return;
		}

		self::deleteCacheNow( $productId );
	}

	private static function deleteCacheNow( $productId ) {
		wp_cache_delete( $productId, 'post_meta' );
		wp_cache_delete( 'product-' . $productId, 'products' );
		delete_transient( 'wc_product_children_' . $productId );
		$wcml_data_store = wcml_product_data_store_cpt();
		$wcml_data_store->update_lookup_table_data( $productId );
	}

	private static function deleteCountTransients() {
		delete_transient( 'wc_low_stock_count' );
		delete_transient( 'wc_outofstock_count' );
	}
}
