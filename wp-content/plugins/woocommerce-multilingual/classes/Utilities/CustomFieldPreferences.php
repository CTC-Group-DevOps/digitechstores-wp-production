<?php

namespace WCML\Utilities;

use WPML\TM\Settings\PreferenceResolver;

class CustomFieldPreferences {

	const ELEMENT_TYPE = 'post';

	public static function modes( array $metaKeys ): array {
		$modes   = PreferenceResolver::modes( self::ELEMENT_TYPE, $metaKeys );
		$unknown = array_keys( $modes, null, true );
		$byRule  = $unknown ? wpml_resolve_custom_field_preferences( $unknown, self::ELEMENT_TYPE ) : [];

		foreach ( $unknown as $metaKey ) {
			$modes[ $metaKey ] = (int) ( $byRule[ $metaKey ] ?? WPML_IGNORE_CUSTOM_FIELD );
		}

		return $modes;
	}

	public static function mode( string $metaKey ): int {
		return self::modes( [ $metaKey ] )[ $metaKey ];
	}
}
