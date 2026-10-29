<?php

namespace WCML\Synchronization\Component;

trait VariationSource {

	protected function isVariationSourceReadable( $variationId, $productId ) {
		if ( ! $this->productHasVariationAttributes( $productId ) ) {
			return true;
		}

		if ( $this->hasAttributeMeta( $variationId ) ) {
			return true;
		}

		wp_cache_delete( $variationId, 'post_meta' );

		return $this->hasAttributeMeta( $variationId );
	}

	private function hasAttributeMeta( $variationId ) {
		foreach ( array_keys( (array) get_post_custom( $variationId ) ) as $metaKey ) {
			if ( 0 === strpos( (string) $metaKey, 'attribute_' ) ) {
				return true;
			}
		}

		return false;
	}

	private function productHasVariationAttributes( $productId ) {
		$productAttributes = get_post_meta( $productId, '_product_attributes', true );
		if ( ! is_array( $productAttributes ) ) {
			return false;
		}

		foreach ( $productAttributes as $productAttribute ) {
			if ( ! empty( $productAttribute['is_variation'] ) ) {
				return true;
			}
		}

		return false;
	}

	protected function reportUnreadableVariationSources( $productId, $variationIds ) {
		if ( empty( $variationIds ) ) {
			return;
		}

		do_action( 'wcml_variation_source_not_readable', $productId, $variationIds );

		if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
			return;
		}

		error_log(
			sprintf(
				'WCML did not translate the variations %s of product %d: the variation meta read returned no attribute values.',
				implode( ',', $variationIds ),
				$productId
			)
		);
	}
}
