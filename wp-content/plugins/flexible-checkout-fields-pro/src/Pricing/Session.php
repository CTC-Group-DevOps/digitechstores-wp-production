<?php

namespace WPDesk\FCF\Pro\Pricing;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FCF\Pro\Pricing\Price;

/**
 * Manages WooCommerce Session.
 */
class Session implements Hookable {

	const SESSION_POST_DATA_KEY = 'fcf-checkout-data';

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'cache_post_data' ] );
		add_action( 'woocommerce_checkout_order_created', [ $this, 'clear_post_data' ] );
	}

	/**
	 * Sets values of checkout fields in session.
	 *
	 * @param string $post_data Values of checkout fields.
	 *
	 * @internal
	 */
	public function cache_post_data( string $post_data ) {
		parse_str( $post_data, $data );
		\WC()->session->set( self::SESSION_POST_DATA_KEY, $data );
	}

	/**
	 * Removes values of checkout fields in session.
	 *
	 * @internal
	 */
	public function clear_post_data() {
		\WC()->session->set( self::SESSION_POST_DATA_KEY, [] );
	}
}
