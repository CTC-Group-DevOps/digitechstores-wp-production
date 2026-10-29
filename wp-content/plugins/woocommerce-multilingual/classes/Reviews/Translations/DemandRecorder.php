<?php

namespace WCML\Reviews\Translations;

class DemandRecorder {

	const OPTION     = 'wcml_review_translation_demand';
	const CRON_HOOK  = 'wcml_reviews_auto_translate';
	const CRON_DELAY = 120;

	const PRODUCT_ELEMENT_TYPE = 'post_product';

	private $automaticTranslation;

	private $demand = [];

	public function __construct( AutomaticTranslation $automaticTranslation ) {
		$this->automaticTranslation = $automaticTranslation;
	}

	public function recordMissingTranslation( $comment ) {
		$productId = (int) \WCML_Comments::getOriginalPostId( $comment );

		if ( ! $productId ) {
			return;
		}

		$targetLanguage = apply_filters( 'wpml_current_language', null );

		if ( ! $targetLanguage ) {
			return;
		}

		$sourceLanguage = apply_filters(
			'wpml_element_language_code',
			null,
			[
				'element_id'   => $productId,
				'element_type' => self::PRODUCT_ELEMENT_TYPE,
			]
		);

		if ( ! $sourceLanguage || $sourceLanguage === $targetLanguage ) {
			return;
		}

		if ( ! $this->automaticTranslation->isAvailableForDefaultLanguageCached() ) {
			return;
		}

		$this->demand[ $productId ][ $targetLanguage ] = true;
	}

	public function flush() {
		if ( ! $this->demand ) {
			return;
		}

		$demand       = $this->demand;
		$this->demand = [];

		$stored = get_option( self::OPTION, [] );

		if ( ! is_array( $stored ) ) {
			$stored = [];
		}

		foreach ( $demand as $productId => $languages ) {
			$existing = isset( $stored[ $productId ] ) && is_array( $stored[ $productId ] )
				? $stored[ $productId ]
				: [];

			$stored[ $productId ] = array_values( array_unique( array_merge( $existing, array_keys( $languages ) ) ) );
		}

		update_option( self::OPTION, $stored, false );

		self::scheduleRun();
	}

	public static function scheduleRun() {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_single_event( time() + self::CRON_DELAY, self::CRON_HOOK );
		}
	}
}
