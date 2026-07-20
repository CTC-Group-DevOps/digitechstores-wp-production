<?php

/*
 * Plugin Name: WooCommerce Conditional Content
 * Plugin URI: https://woocommerce.com/products/woocommerce-conditional-content/
 * Description: WooCommerce conditional content allows you to display additional or alternate content based on a set of criteria.  Criteria includes current users role, product categories, product tags, prices, cart contents, and many more.
 * Version: 2.3.12
 * Author: Element Stark
 * Author URI: https://www.elementstark.com/
 * Requires at least: 3.1
 * Tested up to: 7.0
 * Text Domain: wc_conditional_content
 * Domain Path: /i18n/languages/

 * Copyright: © 2014-2026 Element Stark LLC.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html

 * WC requires at least: 8.0
 * WC tested up to: 10.9
 * Woo: 260119:015e3a0eb801d23217d6fecb97e1537b
 */

/**
 * Required functions
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
	require_once 'woo-includes/woo-functions.php';
}


if ( is_woocommerce_active() ) {

	// Declare support for features.
	add_action(
		'before_woocommerce_init',
		function () {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__ );
			}
		}
	);

	/**
	 * Localisation
	 * */
	load_plugin_textdomain( 'wc_conditional_content', false, dirname( plugin_basename( __FILE__ ) ) . '/' );
	require_once 'woocommerce-conditional-content-main.php';
}
