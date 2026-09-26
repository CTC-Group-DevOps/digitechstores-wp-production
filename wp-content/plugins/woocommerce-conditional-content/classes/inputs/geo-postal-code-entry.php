<?php

class WC_Conditional_Content_Input_Geo_Postal_Code_Entry extends WC_Conditional_Content_Input_Base {

	public function __construct() {
		$this->type = 'Geo_Postal_Code_Entry';

		$this->defaults = array(
			'multiple'      => 0,
			'allow_null'    => 0,
			'choices'       => array(),
			'default_value' => '',
			'class'         => '',
			'placeholder'   => '',
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
				<td style="width:162px;"><?php esc_html_e( 'Distance ( km )', 'wc_conditional_content' ); ?></td>
				<td><?php esc_html_e( 'Zip/Postalcode ( One per line )', 'wc_conditional_content' ); ?></td>
			</tr>
			<tr>
				<td style="width:162px; vertical-align:top;">
					<input
							aria-label="<?php esc_attr_e( 'Quantity', 'wc_conditional_content' ); ?>"
							type="text" id="<?php echo esc_attr( $field['id'] ); ?>_qty"
							name="<?php echo esc_attr( $field['name'] ); ?>[qty]"
							value="<?php echo esc_attr( $value['qty'] ?? 1 ); ?>"/>
				</td>
				<td>
					<textarea
							aria-label="<?php esc_attr_e( 'Zip/Postalcode ( One per line )', 'wc_conditional_content' ); ?>"
							style="width:100%" rows="20" name="<?php esc_attr( $field['name'] ); ?>[codes]"
							type="text" id="<?php echo esc_attr( $field['id'] ); ?>"
							class="<?php echo esc_attr( $field['class'] ); ?>"
							placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"><?php echo esc_textarea( $value['codes'] ); ?></textarea>
				</td>
			</tr>
		</table>
		<?php
	}
}
