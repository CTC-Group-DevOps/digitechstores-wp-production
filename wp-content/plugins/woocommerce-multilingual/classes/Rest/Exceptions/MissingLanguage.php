<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class MissingLanguage extends WC_REST_Exception {

	public function __construct() {
		parent::__construct(
			"422",
			/* translators: Error returned by the WooCommerce REST API. Keep the two quoted parameter names in English: they are what the caller has to send. */
			__( 'Using "translation_of" requires providing a "lang" parameter too', 'woocommerce-multilingual' ),
			422
		);
	}

}