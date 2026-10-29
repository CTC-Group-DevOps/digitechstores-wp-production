<?php

namespace WCML\Attributes;

use Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore as ProductAttributesLookupDataStore;
use WCML\Terms\SuspendWpmlFiltersFactory;
use WPML\Convert\Ids;
use WPML\FP\Obj;
use WPML\LIB\WP\Hooks;
use function WPML\FP\spreadArgs;
use WPML\FP\Fns;

class LookupTable implements \IWPML_Action {

	const PRIORITY_BEFORE_LOOKUP_CALLBACK = 5;
	const PRIORITY_AFTER_LOOKUP_CALLBACK  = 20;

	private $sitepress;

	private $filtersSuspend;

	public function __construct( \SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		Hooks::onAction( 'save_post' )
			->then( spreadArgs( [ $this, 'triggerUpdateForTranslations' ] ) );

		Hooks::onAction( 'woocommerce_run_product_attribute_lookup_update_callback', self::PRIORITY_BEFORE_LOOKUP_CALLBACK )
			->then( [ $this, 'suspendTermsFilters' ] );
		Hooks::onAction( 'woocommerce_run_product_attribute_lookup_update_callback', self::PRIORITY_AFTER_LOOKUP_CALLBACK )
			->then( [ $this, 'resumeTermsFilters' ] );

		Hooks::onFilter( 'woocommerce_attribute_lookup_regeneration_step_size' )
			->then( spreadArgs( Fns::tap( [ $this, 'regenerateTable' ] ) ) );
		Hooks::onAction( 'woocommerce_run_product_attribute_lookup_regeneration_callback', self::PRIORITY_AFTER_LOOKUP_CALLBACK )
			->then( [ $this, 'resumeTermsFilters' ] );

		Hooks::onFilter( 'pre_update_option_woocommerce_attribute_lookup_last_product_id_to_process' )
			->then( spreadArgs( [ $this, 'setLastProductIdAcrossLanguages' ] ) );
	}

	public function triggerUpdateForTranslations( $productId ) {
		if (
			'product' === get_post_type( $productId )
			&& 'publish' === get_post_status( $productId )
			&& ! $this->sitepress->is_original_content_filter( false, $productId, 'post_product' )
		) {
			$savedOnBlogId = get_current_blog_id();

			Hooks::onAction( 'shutdown' )
				->then( function() use ( $productId, $savedOnBlogId ) {
					$this->refreshLookupTable( $productId, $savedOnBlogId );
				} );
		}
	}

	private function refreshLookupTable( $productId, $savedOnBlogId ) {
		$savedOnAnotherBlog    = get_current_blog_id() !== $savedOnBlogId;
		$hasTermsClausesFilter = null;

		if ( $savedOnAnotherBlog ) {
			switch_to_blog( $savedOnBlogId );
		}

		try {
			$product = wc_get_product( $productId );

			if ( $product ) {
				$hasTermsClausesFilter = $this->adjustTermsFilters();

				wc_get_container()->get( ProductAttributesLookupDataStore::class )->on_product_changed( $product );
			}
		} catch ( \Throwable $e ) {
			return;
		} finally {
			if ( $hasTermsClausesFilter ) {
				$this->restoreTermsFilters( $hasTermsClausesFilter );
			}

			if ( $savedOnAnotherBlog ) {
				restore_current_blog();
			}
		}
	}

	public function suspendTermsFilters() {
		if ( ! $this->filtersSuspend ) {
			$this->filtersSuspend = $this->adjustTermsFilters();
		}
	}

	public function resumeTermsFilters() {
		if ( $this->filtersSuspend ) {
			$this->restoreTermsFilters( $this->filtersSuspend );
			$this->filtersSuspend = null;
		}
	}

	public function adjustTermsFilters() {
		add_filter( 'woocommerce_product_get_attributes', [ $this, 'translateAttributeOptions' ], 10, 2 );
		add_filter( 'woocommerce_product_variation_get_attributes', [ $this, 'translateVariationTerms' ], 10, 2 );

		return SuspendWpmlFiltersFactory::create();
	}

	private function restoreTermsFilters( $filtersSuspend ) {
		$filtersSuspend->resume();

		remove_filter( 'woocommerce_product_get_attributes', [ $this, 'translateAttributeOptions' ] );
		remove_filter( 'woocommerce_product_variation_get_attributes', [ $this, 'translateVariationTerms' ] );
	}

	public function translateAttributeOptions( $attributes, $product ) {
		$language = $this->sitepress->get_language_for_element(
			$product->get_id(),
			'post_product'
		);

		if ( $language ) {
			$getTranslatedOptions = function( $attribute, $taxonomy ) use ( $language ) {
				$attribute->set_options( Ids::convert( $attribute->get_options(), $taxonomy, true, $language ) );

				return $attribute;
			};

			$attributes = wpml_collect( $attributes )
				->map( $getTranslatedOptions )
				->toArray();
		}

		return $attributes;
	}

	public function translateVariationTerms( $attributes, $product ) {
		$language = $this->sitepress->get_language_for_element(
			$product->get_id(),
			'post_product_variation'
		);

		if ( $language ) {
			$getTranslatedSlug = function( $slug, $taxonomy ) use ( $language ) {
				$term = get_term_by( 'slug', $slug, $taxonomy );
				if ( false === $term ) {
					return $slug;
				}
				$termId         = Ids::convert( $term->term_id, $taxonomy, true, $language );
				$translatedTerm = get_term( $termId, $taxonomy );

				return Obj::prop( 'slug', $translatedTerm );
			};

			$attributes = wpml_collect( $attributes )
				->map( $getTranslatedSlug )
				->toArray();
		}

		return $attributes;
	}

	public function regenerateTable() {
		$this->suspendTermsFilters();
		$this->suppressLanguageFilterOnProductQueries();
	}

	public function setLastProductIdAcrossLanguages( $lastProductId ) {
		$this->suppressLanguageFilterOnProductQueries();

		$ids = wc_get_products(
			[
				'return'  => 'ids',
				'limit'   => 1,
				'orderby' => [ 'ID' => 'DESC' ],
			]
		);

		return $ids ? (int) $ids[0] : $lastProductId;
	}

	private function suppressLanguageFilterOnProductQueries() {
		add_filter( 'woocommerce_product_object_query_args', Obj::assoc( 'suppress_filters', true ) );
	}

}
