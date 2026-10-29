<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class InvalidCurrency extends WC_REST_Exception {

	public function __construct( $currency_code ) {
		parent::__construct(
			"422",
			/* translators: Error returned by the WooCommerce REST API when the currency asked for is not one of the shop's. %s: the currency code that was sent. */
			sprintf( __( 'Invalid currency parameter: "%s"', 'woocommerce-multilingual' ),
				$currency_code ),
			422
		);
	}
}