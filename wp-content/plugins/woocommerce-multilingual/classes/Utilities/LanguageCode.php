<?php

namespace WCML\Utilities;

class LanguageCode {

	const ENGLISH = 'en';

	public static function isEnglish( $code ) {
		if ( class_exists( \WPML\Core\SharedKernel\Component\Language\Domain\LanguageCode::class ) ) {
			return \WPML\Core\SharedKernel\Component\Language\Domain\LanguageCode::isEnglish( $code );
		}

		return self::ENGLISH === strtolower( trim( (string) $code ) );
	}
}
