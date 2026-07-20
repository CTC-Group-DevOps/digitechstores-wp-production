<?php

class WC_Conditional_Content_Input_Cart_Product_Select extends WC_Conditional_Content_Input_Base {
	public function __construct() {
		$this->type = 'Cart_Product_Select';

		$this->defaults = array(
			'multiple'      => 0,
			'allow_null'    => 0,
			'choices'       => array(),
			'default_value' => '',
			'class'         => 'wc-product-select',
		);
	}

	public function render( $field, $value = null ): void {
		$field = array_merge( $this->defaults, $field );
		if ( ! isset( $field['id'] ) ) {
			$field['id'] = sanitize_title( $field['id'] );
		}
		?>

		<table style="width:100%;">
			<tr>
				<td style="width:32px;"><?php esc_html_e( 'Quantity', 'wc_conditional_content' ); ?></td>
				<td><?php esc_html_e( 'Products', 'wc_conditional_content' ); ?></td>
			</tr>
			<tr>
				<td style="width:32px; vertical-align:top;">
					<input aria-label="<?php esc_attr_e( 'Quantity', 'wc_conditional_content' ); ?>" type="text"
							id="<?php echo esc_attr( $field['id'] ); ?>_qty"
							name="<?php echo esc_attr( $field['name'] ); ?>[qty]"
							value="<?php echo esc_attr( $value['qty'] ?? 1 ); ?>"/>

				</td>
				<td>
					<select aria-label="<?php esc_attr_e( 'Select a product', 'wc_conditional_content' ); ?>"
							id="<?php echo esc_attr( $field['id'] ); ?>"
							style="width: 100%;"
							name="<?php echo esc_attr( $field['name'] ); ?>[products][]"
							class="wc-product-search"
							data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>"
							data-action="woocommerce_json_search_products_and_variations"
							multiple="multiple"
							data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>">
						<?php
						$current     = $value['products'] ?? array();
						$product_ids = ! empty( $current ) ? array_map( 'absint', $current ) : null;
						if ( $product_ids ) {
							foreach ( $product_ids as $product_id ) {
								if ( 0 === $product_id ) {
									echo '<option value="0" selected="selected">' . esc_html__( 'Any', 'wc_conditional_content' ) . '</option>';
								} else {
									$product      = wc_get_product( $product_id );
									$product_name = WC_Conditional_Content_Compatibility::woocommerce_get_formatted_product_name( $product );

									echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . esc_html( $product_name ) . '</option>';
								}
							}
						} else {
							echo '<option value="0">' . esc_html__( 'Any', 'wc_conditional_content' ) . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
		</table>


		<?php
	}
}
