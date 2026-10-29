<?php

namespace WCML\Setup;

use WPML\FP\Fns;

class BeforeHooks implements \IWPML_Backend_Action, \IWPML_Frontend_Action, \IWPML_DIC_Action {

	private const BLOCKED_POST_TYPES = [ 'product', 'product_variation' ];

	private $woocommerce_wpml;

	public function __construct( \woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		$pendingWizard = ! $this->woocommerce_wpml->get_setting( 'set_up_wizard_run' );
		if ( $pendingWizard ) {
			add_filter( 'get_translatable_documents_all', [ __CLASS__, 'blockProductTranslation' ] );
			add_filter( 'wpml_exclude_post_from_auto_translate', [ __CLASS__, 'excludeProductFromAutoTranslate' ], 10, 2 );

			if ( $this->isStringTranslationActive() ) {
				add_filter( 'wpml_wizard_display_wcml_messages', Fns::always( true ) );
			}
		}
	}

	public static function blockProductTranslation( $translatablePostTypes ) {
		foreach ( self::BLOCKED_POST_TYPES as $postType ) {
			unset( $translatablePostTypes[ $postType ] );
		}

		return $translatablePostTypes;
	}

	public static function excludeProductFromAutoTranslate( $isExcluded, $postId ) {
		return $isExcluded || in_array( get_post_type( $postId ), self::BLOCKED_POST_TYPES, true );
	}

	private function isStringTranslationActive(): bool {
		return defined( 'WPML_ST_VERSION' );
	}
}
