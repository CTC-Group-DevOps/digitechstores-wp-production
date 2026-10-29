<?php

namespace WCML\Reviews;

use WCML\Reviews\Translations\AutomaticTranslation;
use WCML\Reviews\Translations\FrontEndHooks;

use function WCML\functions\getSetting;

class AllLanguagesSetting {

	const SETTING = 'reviews_in_all_languages';

	public static function resolve( $stored, AutomaticTranslation $automaticTranslation ) {
		if ( null !== $stored ) {
			return (bool) $stored;
		}

		return self::isDefaultOn( $automaticTranslation );
	}

	public static function isDefaultOn( AutomaticTranslation $automaticTranslation ) {
		return (bool) apply_filters( FrontEndHooks::HOOK_ENABLE, true )
			&& $automaticTranslation->isAvailableForDefaultLanguageCached();
	}

	public static function isOn( ?AutomaticTranslation $automaticTranslation = null ) {
		if ( ! $automaticTranslation ) {
			$automaticTranslation = new AutomaticTranslation();
		}

		return self::resolve( getSetting( self::SETTING, null ), $automaticTranslation );
	}
}
