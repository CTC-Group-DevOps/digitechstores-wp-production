<?php

namespace WCML\Rest\Exceptions;

use WC_REST_Exception;

class InvalidTerm extends WC_REST_Exception {

	public function __construct( $term_id ) {
		parent::__construct(
			"422",
			/* translators: Error returned by the WooCommerce REST API when no product category, tag or attribute value has the requested identifier. %d: its numeric ID. */
			sprintf( __( 'Term not found: %d', 'woocommerce-multilingual' ),
				$term_id ),
			422
		);
	}

}