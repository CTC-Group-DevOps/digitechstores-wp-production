<?php

namespace WCML\Rest\Store;

use WCML\Reviews\AllLanguagesSetting;
use WPML\FP\Fns;

class ReviewsHooks implements \IWPML_Action {

	public function add_hooks() {
		if ( AllLanguagesSetting::isOn() ) {
			add_action( 'wpml_is_comment_query_filtered', Fns::always( false ) );
		}
	}
}
