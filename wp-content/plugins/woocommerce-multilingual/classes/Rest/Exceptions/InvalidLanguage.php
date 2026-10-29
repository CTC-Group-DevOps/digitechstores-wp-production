<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class InvalidLanguage extends WC_REST_Exception {

	public function __construct( $language_code ) {
		parent::__construct(
			"422",
			/* translators: Error returned by the WooCommerce REST API when the language asked for is not active on the site. %s: the language code that was sent. */
			sprintf( __( 'Invalid language parameter: "%s"', 'woocommerce-multilingual' ),
				$language_code ),
			422
		);
	}

}