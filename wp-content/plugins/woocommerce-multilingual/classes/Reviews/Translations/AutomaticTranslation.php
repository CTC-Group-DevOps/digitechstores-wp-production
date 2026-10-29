<?php

namespace WCML\Reviews\Translations;

use WPML\TM\ATE\AutomaticTranslationCapabilities;

class AutomaticTranslation {

	const AVAILABILITY_OPTION = 'wcml_reviews_automatic_translation';

	public function isPairEligible( $sourceLanguage, $targetLanguage ) {
		if ( ! $sourceLanguage || ! $targetLanguage || $sourceLanguage === $targetLanguage ) {
			return false;
		}

		if ( ! class_exists( AutomaticTranslationCapabilities::class ) ) {
			return false;
		}

		if ( ! $this->isTranslateEverythingOn() ) {
			return false;
		}

		return (bool) AutomaticTranslationCapabilities::isLanguageEligible( $targetLanguage, $sourceLanguage );
	}

	private function isTranslateEverythingOn() {
		if ( ! class_exists( AutomaticTranslationCapabilities::class ) ) {
			return false;
		}

		if ( method_exists( AutomaticTranslationCapabilities::class, 'shouldTranslateEverythingFresh' ) ) {
			return (bool) AutomaticTranslationCapabilities::shouldTranslateEverythingFresh();
		}

		return (bool) AutomaticTranslationCapabilities::shouldTranslateEverything();
	}

	public function isAvailableForDefaultLanguageCached() {
		if ( $this->canWaitForAte() ) {
			return $this->refreshAvailability();
		}

		return (bool) get_option( self::AVAILABILITY_OPTION, false );
	}

	public function refreshAvailability() {
		$available = $this->isAvailableForDefaultLanguage();

		update_option( self::AVAILABILITY_OPTION, $available ? 1 : 0, true );

		return $available;
	}

	private function canWaitForAte() {
		return ( is_admin() && ! wp_doing_ajax() ) || wp_doing_cron();
	}

	public function isAvailableForDefaultLanguage() {
		if ( ! class_exists( AutomaticTranslationCapabilities::class ) ) {
			return false;
		}

		if ( ! $this->isTranslateEverythingOn() ) {
			return false;
		}

		return (bool) AutomaticTranslationCapabilities::doesDefaultLanguageSupport();
	}
}
