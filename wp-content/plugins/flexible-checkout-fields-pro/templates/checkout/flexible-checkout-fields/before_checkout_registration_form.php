<?php
/**
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields-pro/checkout/flexible-checkout-fields/before_checkout_registration_form.php
 *
 * @var array        $fields           .
 * @var \WC_Checkout $checkout         .
 * @var array        $section_settings .
 * @var string       $html_tag         .
 * @var string       $section_id       .
 *
 * @package Flexible Checkout Fields PRO
 */

?>
<div class="flexible-checkout-fields-<?php echo esc_attr( $section_id ); ?> <?php echo esc_attr( $section_settings['section_css'] ?? '' ); ?>">

    <?php if ( $html_tag && isset( $section_settings['section_title'] ) && $section_settings['section_title'] != '' ) : ?>
        <<?php echo esc_attr( $html_tag ); ?>>
            <?php echo wp_kses( $section_settings['section_title'], '' ); ?>
        </<?php echo esc_attr( $html_tag ); ?>>
    <?php endif; ?>

    <?php
        foreach ( $fields as $key => $field ) {
            woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
        }
    ?>

</div>
