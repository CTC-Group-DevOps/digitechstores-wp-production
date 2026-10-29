<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class InvalidProduct extends WC_REST_Exception {

	public function __construct( $product_id ) {
		parent::__construct(
			"422",
			/* translators: Error returned by the WooCommerce REST API when no product has the requested identifier. %d: the product's numeric ID. */
			sprintf( __( 'Product not found: %d', 'woocommerce-multilingual' ),
				$product_id ),
			422
		);
	}

}