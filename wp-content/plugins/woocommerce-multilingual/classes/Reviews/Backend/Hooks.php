<?php

namespace WCML\Reviews\Backend;

use SitePress;
use WCML\Reviews\Translations\AutomaticTranslation;
use WCML\Reviews\Translations\FrontEndHooks;

use function WPML\Container\make;

class Hooks implements \IWPML_Backend_Action, \IWPML_DIC_Action {

	const AFTER_SITEPRESS_JS_LOAD = 3;

	private $sitepress;

	private $automaticTranslation;

	public function __construct( SitePress $sitepress, ?AutomaticTranslation $automaticTranslation = null ) {
		$this->sitepress            = $sitepress;
		$this->automaticTranslation = $automaticTranslation;
	}

	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'disableLanguageQuickLinks' ], self::AFTER_SITEPRESS_JS_LOAD );

		if ( apply_filters( FrontEndHooks::HOOK_ENABLE, true ) ) {
			add_action( 'admin_init', [ $this, 'refreshAutomaticTranslationAvailability' ] );
		}
	}

	public function refreshAutomaticTranslationAvailability() {
		$this->getAutomaticTranslation()->isAvailableForDefaultLanguageCached();
	}

	public function disableLanguageQuickLinks() {
		$getData = wpml_collect( $_GET );

		if ( 'product' === $getData->get( 'post_type' ) && 'product-reviews' === $getData->get( 'page' ) ) {
			remove_action( 'admin_enqueue_scripts', [ $this->sitepress, 'language_filter' ] );
		}
	}

	private function getAutomaticTranslation() {
		if ( ! $this->automaticTranslation ) {
			$this->automaticTranslation = make( AutomaticTranslation::class );
		}

		return $this->automaticTranslation;
	}

}
