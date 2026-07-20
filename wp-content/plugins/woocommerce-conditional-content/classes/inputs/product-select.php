<?php

class WC_Conditional_Content_Input_Product_Select extends WC_Conditional_Content_Input_Base {

    public function __construct() {
        // vars
        $this->type = 'Product_Select';

        $this->defaults = array(
                'multiple'      => 0,
                'allow_null'    => 0,
                'choices'       => array(),
                'default_value' => '',
                'class'         => 'ajax_chosen_select_products',
        );
    }

    public function render( $field, $value = null ): void {

        $field = array_merge( $this->defaults, $field );
        if ( ! isset( $field['id'] ) ) {
            $field['id'] = sanitize_title( $field['id'] );
        }

        ?>
        <select
                aria-label="<?php esc_attr_e( 'Select Product', 'wc_conditional_content' ); ?>"
                class="wc-product-search" multiple="multiple" style="width: 100%;"
                id="<?php echo esc_attr( $field['id'] ); ?>"
                name="<?php echo esc_attr( $field['name'] ); ?>[]"
                data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>"
                data-action="woocommerce_json_search_products">
            <?php
            $current     = ! empty( $value ) ? $value : array();
            $product_ids = ! empty( $current ) ? array_map( 'absint', $current ) : null;
            if ( $product_ids ) {
                foreach ( $product_ids as $product_id ) {

                    $product = wc_get_product( $product_id );
                    if ( ! ( $product instanceof WC_Product ) ) {
                        continue;
                    }

                    $product_name = WC_Conditional_Content_Compatibility::woocommerce_get_formatted_product_name( $product );
                    echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . esc_html( $product_name ) . '</option>';
                }
            }
            ?>
        </select>
        <?php
    }
}
