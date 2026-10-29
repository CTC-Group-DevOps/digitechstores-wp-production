<?php

namespace WCML\Permalinks;

use IWPML_Backend_Action;
use IWPML_DIC_Action;
use IWPML_Frontend_Action;
use WCML\Utilities\WCTaxonomies;
use wpdb;
use WPML_Slug_Translation_Factory;
use WPML_ST_String;
class TaxonomySlugTranslationActivator implements IWPML_Backend_Action, IWPML_Frontend_Action, IWPML_DIC_Action {

	private $wpdb;

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function add_hooks() {
		add_action( 'wpml_st_add_string_translation', [ $this, 'maybeActivate' ], 10, 4 );
	}

	public function maybeActivate( $st_id, $translation_data, $language, $string_id ) {
		if ( ! class_exists( WPML_Slug_Translation_Factory::class ) || ! class_exists( WPML_ST_String::class ) ) {
			return;
		}

		static $taxonomyByStringName = null;
		if ( null === $taxonomyByStringName ) {
			$taxonomyByStringName = [
				Strings::getStringName( WCTaxonomies::TAXONOMY_PRODUCT_CATEGORY ) => WCTaxonomies::TAXONOMY_PRODUCT_CATEGORY,
				Strings::getStringName( WCTaxonomies::TAXONOMY_PRODUCT_TAG )      => WCTaxonomies::TAXONOMY_PRODUCT_TAG,
			];
		}

		$string = new WPML_ST_String( $string_id, $this->wpdb );

		if ( Strings::TRANSLATION_DOMAIN !== $string->get_context() ) {
			return;
		}

		$name = $string->get_name();
		if ( ! isset( $taxonomyByStringName[ $name ] ) ) {
			return;
		}

		do_action(
			'wpml_activate_slug_translation',
			$taxonomyByStringName[ $name ],
			$string->get_value(),
			WPML_Slug_Translation_Factory::TAX
		);
	}
}
