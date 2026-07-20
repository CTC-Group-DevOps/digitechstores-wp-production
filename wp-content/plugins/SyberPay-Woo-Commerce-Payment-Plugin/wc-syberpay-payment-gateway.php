<?php
/*
Plugin Name: WooCommerce SyberPay Payment Gateway Test
Plugin URI: http://www.sybertechnology.com/
Description: SyberPay payment gateway allows you to accept payment on your Woocommerce store via banks physical/virtual Cards in Sudan.
Version: 1.2.0
Author: syberTeam
Author URI: http://www.sybertechnology.com/

    Copyright: © 2014 sybertechnology.
    License: GNU General Public License v3.0
    License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Include our Gateway Class and Register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'wc_syberpay_init', 0 );
function wc_syberpay_init() {
    
    // If the parent WC_Payment_Gateway class doesn't exist
    // it means WooCommerce is not installed on the site
    // so do nothing
    if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
     
    // If we made it this far, then include our Gateway Class
    include_once( 'wc-syberpay.php' );
 
    // Now that we have successfully included our class,
    // Lets add it too WooCommerce
    add_filter( 'woocommerce_payment_gateways', 'wc_add_syberpay_gateway' );
    function wc_add_syberpay_gateway( $methods ) {
        $methods[] = 'WC_SyberPay_Gateway';
        return $methods;
    }
    
    //  Filter Gateways 
    //  Only show SyberPay payment method if currency is sudanese pound
    // add_filter('woocommerce_available_payment_gateways','filter_gateways',1);
    // function filter_gateways($gateways){
    //     $current_currency = get_woocommerce_currency_symbol(); 

    //     if ( $current_currency != "SDG") {
    //         unset($gateways['syberpay']);
    //     }

    //     return $gateways;
    // }

}
 
 
// Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'syberpay_plugin_action_links' );
function syberpay_plugin_action_links( $links ) {
    
    $plugin_links = array(
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'wc_syberpay_gateway' ) . '</a>',
    );
 
    // Merge our new link with the default ones
    return array_merge( $plugin_links, $links );    
}


?>