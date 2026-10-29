<?php

class WCML_Cart_Removed_Items_Widget extends WP_Widget {

    public function __construct() {

        $widget_opt = [
			/* translators: Description of the widget in the WordPress widgets screen; "it" is the widget. */
            'description' => __( 'Shows a list of the products that existed in the cart before the cart is reset on the front end after switching the language or the currency. It will be hidden when there are no products to show.', 'woocommerce-multilingual' ),
        ];

        parent::__construct(
            'wcml_cart_deleted_items',
			/* translators: Name of the widget in the WordPress widgets screen; it lists what the shopper had in the cart before switching language or currency. */
            __( 'Products before cart reset', 'woocommerce-multilingual' ),
            $widget_opt
        );

    }

    public function widget( $args, $instance ) {

        echo $args[ 'before_widget' ];

        do_action( 'wcml_removed_cart_items' );

        echo $args[ 'after_widget' ];
    }

}