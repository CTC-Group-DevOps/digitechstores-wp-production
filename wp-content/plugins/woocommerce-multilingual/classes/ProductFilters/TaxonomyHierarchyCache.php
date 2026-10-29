<?php

namespace WCML\ProductFilters;

use WCML\Utilities\WCTaxonomies;
use WPML\LIB\WP\Hooks as WpHooks;
use function WPML\FP\spreadArgs;

class TaxonomyHierarchyCache implements \IWPML_Frontend_Action, \IWPML_Backend_Action {

	const OPTION_PREFIX = 'wc_taxonomy_hierarchy_';

	const TRANSIENT_VERSION_KEY = 'filter_data-transient-version';

	public function add_hooks() {
		WpHooks::onFilter( 'transient_' . self::TRANSIENT_VERSION_KEY )
				->then( spreadArgs( [ $this, 'appendLanguageToVersion' ] ) );

		foreach ( $this->getTaxonomies() as $taxonomy ) {
			$option = self::OPTION_PREFIX . $taxonomy;

			WpHooks::onFilter( 'pre_option_' . $option )
					->then( spreadArgs( function ( $preOption ) use ( $taxonomy ) {
						return $this->readLanguageOption( $preOption, $taxonomy );
					} ) );

			WpHooks::onFilter( 'pre_update_option_' . $option, 10, 2 )
					->then( spreadArgs( function ( $value, $oldValue ) use ( $taxonomy ) {
						return $this->writeLanguageOption( $value, $oldValue, $taxonomy );
					} ) );
		}

		WpHooks::onAction( 'created_term', 10, 3 )->then( spreadArgs( [ $this, 'clearOnTermChange' ] ) );
		WpHooks::onAction( 'edited_term', 10, 3 )->then( spreadArgs( [ $this, 'clearOnTermChange' ] ) );
		WpHooks::onAction( 'delete_term', 10, 3 )->then( spreadArgs( [ $this, 'clearOnTermChange' ] ) );

		WpHooks::onAction( 'added_term_meta', 10, 4 )->then( spreadArgs( [ $this, 'clearOnTermMetaChange' ] ) );
		WpHooks::onAction( 'updated_term_meta', 10, 4 )->then( spreadArgs( [ $this, 'clearOnTermMetaChange' ] ) );
		WpHooks::onAction( 'deleted_term_meta', 10, 4 )->then( spreadArgs( [ $this, 'clearOnTermMetaChange' ] ) );
	}

	public function appendLanguageToVersion( $value ) {
		if ( ! is_string( $value ) || '' === $value ) {
			return $value;
		}

		$language = apply_filters( 'wpml_current_language', null );

		if ( ! is_string( $language ) || '' === $language ) {
			return $value;
		}

		return $value . '-' . $language;
	}

	public function readLanguageOption( $preOption, $taxonomy ) {
		if ( false !== $preOption ) {
			return $preOption;
		}

		$key = $this->getLanguageOptionKey( $taxonomy );

		return null === $key ? $preOption : get_option( $key, [] );
	}

	public function writeLanguageOption( $value, $oldValue, $taxonomy ) {
		$key = $this->getLanguageOptionKey( $taxonomy );

		if ( null === $key ) {
			return $value;
		}

		update_option( $key, $value, false );

		return $oldValue;
	}

	public function clearOnTermChange( $term_id, $tt_id, $taxonomy ) {
		$this->clearLanguageOptions( $taxonomy );
	}

	public function clearOnTermMetaChange( $meta_id, $term_id, $meta_key, $meta_value ) {
		if ( 'order' !== $meta_key ) {
			return;
		}

		$term = get_term( $term_id );

		if ( $term instanceof \WP_Term ) {
			$this->clearLanguageOptions( $term->taxonomy );
		}
	}

	private function clearLanguageOptions( $taxonomy ) {
		if ( ! in_array( $taxonomy, $this->getTaxonomies(), true ) ) {
			return;
		}

		$languages = apply_filters( 'wpml_active_languages', null, [ 'skip_missing' => 0 ] );

		if ( ! is_array( $languages ) ) {
			return;
		}

		foreach ( array_keys( $languages ) as $language ) {
			$key = $this->getLanguageOptionKey( $taxonomy, $language );

			if ( null !== $key ) {
				delete_option( $key );
			}
		}
	}

	private function getLanguageOptionKey( $taxonomy, $language = null ) {
		if ( null === $language ) {
			$language = apply_filters( 'wpml_current_language', null );
		}

		if ( ! is_string( $language ) || '' === $language ) {
			return null;
		}

		return self::OPTION_PREFIX . $taxonomy . '-' . $language;
	}

	private function getTaxonomies() {
		return [ WCTaxonomies::TAXONOMY_PRODUCT_CATEGORY ];
	}
}
