<?php

namespace WCML\PointerUi;

use WPML\FP\Obj;
use WCML_Pointer_UI;

class Factory {

	public static function create( $args ) {
		$defaultArgs = [
			/* translators: Default link text of the help bubble shown next to a setting that WPML translates; "this" is the setting the bubble sits on. */
			'anchor'     => __( 'How to translate this?', 'woocommerce-multilingual' ),
			'content'    => '',
			'docLink'    => null,
			'selectorId' => null,
			'method'     => 'after',
		];
		$args        = wp_parse_args( $args, $defaultArgs );

		return new WCML_Pointer_UI(
			Obj::prop( 'content', $args ),
			Obj::prop( 'docLink', $args ),
			Obj::prop( 'selectorId', $args ),
			Obj::prop( 'method', $args ),
			Obj::prop( 'anchor', $args )
		);
	}

}
