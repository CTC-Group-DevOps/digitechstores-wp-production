<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );

add_filter( 'woocommerce_my_account_my_orders_actions', 'remove_pay_cancel_from_order_received', 10, 2 );
function remove_pay_cancel_from_order_received( $actions, $order ) {

    // Remove Pay button
    if ( isset( $actions['pay'] ) ) {
        unset( $actions['pay'] );
    }

    // Remove Cancel button
    if ( isset( $actions['cancel'] ) ) {
        unset( $actions['cancel'] );
    }

    return $actions;
}

/**
 * Change Add to Cart button text on shop & category pages
 */
add_filter( 'woocommerce_product_add_to_cart_text', 'wd_change_loop_button_text', 10, 2 );
function wd_change_loop_button_text( $text, $product ) {

    if ( is_shop() || is_product_category() ) {
        return __( 'Read more', 'woocommerce' );
    }

    return $text;
}

/**
 * Change Add to Cart button link
 */
add_filter( 'woocommerce_loop_add_to_cart_link', 'wd_change_loop_button_link', 20, 2 );
function wd_change_loop_button_link( $link, $product ) {

    // Apply on Home widgets, Shop, Category, sliders, tabs
    // Skip only single product page
    if ( ! is_product() ) {

        $product_url = get_permalink( $product->get_id() );

        return '<a href="' . esc_url( $product_url ) . '" 
            class="button custom_addtocart_btn product_type_' . esc_attr( $product->get_type() ) . '">
            <span>' . esc_html__( 'Read more', 'woocommerce' ) . '</span>
        </a>';
    }

    return $link;
}

/* Disable AJAX add to cart everywhere except single product */
add_filter( 'woodmart_ajax_add_to_cart', '__return_false' );

add_filter( 'woocommerce_product_supports', 'wd_disable_ajax_on_loop', 10, 3 );
function wd_disable_ajax_on_loop( $supports, $feature, $product ) {

    if ( $feature === 'ajax_add_to_cart' && ! is_product() ) {
        return false;
    }

    return $supports;
}



/* add_filter( 'gettext', 'change_woocommerce_variation_message', 20, 3 );
function change_woocommerce_variation_message( $translated_text, $text, $domain ) {

    if ( $domain === 'woocommerce' && $text === 'Please select some product options before adding this product to your cart.' ) {
        $translated_text = 'Please select product region before adding this product to your cart.'; // Your custom message
    }

    return $translated_text;
} */


add_action( 'init', function() {
    do_action(
        'wpml_register_single_string',
        'Custom Messages',
        'Variation Message',
        'Please select product region before adding this product to your cart.'
    );
});

add_filter( 'gettext', 'change_woocommerce_variation_message', 20, 3 );
function change_woocommerce_variation_message( $translated_text, $text, $domain ) {

    if ( $domain === 'woocommerce' && $text === 'Please select some product options before adding this product to your cart.' ) {

        $translated_text = apply_filters(
            'wpml_translate_single_string',
            'Please select product region before adding this product to your cart.',
            'Custom Messages',
            'Variation Message'
        );
    }

    return $translated_text;
}

/* Munzir */
//add_filter( 'woocommerce_defer_product_sync', '__return_false' );
/* Munzir */