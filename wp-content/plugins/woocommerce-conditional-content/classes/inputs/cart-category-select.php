<?php

class WC_Conditional_Content_Input_Cart_Category_Select extends WC_Conditional_Content_Input_Base {

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		$this->type = 'Cart_Category_Select';

		$this->defaults = array(
			'multiple'      => 0,
			'allow_null'    => 0,
			'choices'       => array(),
			'default_value' => array(),
			'class'         => '',
		);
	}

	/**
	 * Render the input field.
	 *
	 * @param array $field The field data.
	 * @param null  $value The value of the field to be used for rendering.
	 */
	public function render( $field, $value = null ): void {

		$field = array_merge( $this->defaults, $field );
		if ( ! isset( $field['id'] ) ) {
			$field['id'] = sanitize_title( $field['id'] );
		}

		$current = $value['categories'] ?? array();
		$current = array_map( 'absint', $current );
		$choices = $field['choices'];
		?>
		<table style="width:100%;">
			<tr>
				<td style="width:32px;"><?php esc_html_e( 'Quantity', 'wc_conditional_content' ); ?></td>
				<td><?php esc_html_e( 'Categories', 'wc_conditional_content' ); ?></td>
			</tr>
			<tr>
				<td style="width:32px; vertical-align:top;">
					<input aria-label="<?php esc_attr_e( 'Quantity', 'wc_conditional_content' ); ?>" type="text"
							id="<?php echo esc_attr( $field['id'] ); ?>_qty"
							name="<?php echo esc_attr( $field['name'] ); ?>[qty]"
							value="<?php echo isset( $value['qty'] ) ? esc_attr( $value['qty'] ) : 1; ?>"/>
				</td>
				<td>
					<select aria-label="<?php esc_attr_e( 'Category', 'wc_conditional_content' ); ?>"
							style="width: 100%;"
							id="<?php echo esc_attr( $field['id'] ); ?>"
							name="<?php echo esc_attr( $field['name'] ); ?>[categories][]"
							class="wc-enhanced-select <?php echo esc_attr( $field['class'] ); ?>" multiple="multiple"
							data-placeholder="<?php echo esc_attr( $field['placeholder'] ?? __( 'Search...', 'wc_conditional_content' ) ); ?>">
						<?php
						foreach ( $choices as $choice => $title ) {
							$selected = in_array( $choice, $current, true );
							echo '<option value="' . esc_attr( $choice ) . '" ' . selected( $selected, true, false ) . '">' . esc_html( $title ) . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
		</table>

		<?php
	}
}
