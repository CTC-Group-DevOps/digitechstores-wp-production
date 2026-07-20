<?php

class WC_Conditional_Content_Input_Text_Text extends WC_Conditional_Content_Input_Base {

	public function __construct() {
		// vars
		$this->type = 'Text_Text';
	}

	public function render( $field, $value = null ): void {
		$field = array_merge( $this->defaults, $field );
		if ( ! isset( $field['id'] ) ) {
			$field['id'] = sanitize_title( $field['id'] );
		}

		$field_key = $value['field_key'] ?? '';
        $field_value = $value['field_value'] ?? '';

		?>

		<table style="width:100%;">
			<tr>
				<td><?php esc_html_e( 'Field Key', 'wc_conditional_content' ); ?></td>
				<td style="width:162px;"><?php esc_html_e( 'Field Value', 'wc_conditional_content' ); ?></td>
			</tr>
			<tr>
				<td style="width:32px">
					<input
							aria-label="<?php esc_attr_e( 'Field Key', 'wc_conditional_content' ); ?>"
							type="text" id="<?php echo esc_attr( $field['id'] ); ?>_field_key"
							name="<?php echo esc_attr( $field['name'] ); ?>[field_key]"
							value="<?php echo esc_attr( $field_key ?? '' ); ?>"/>
				</td>
				<td>
                    <input
                            aria-label="<?php esc_attr_e( 'Field Value', 'wc_conditional_content' ); ?>"
                            type="text" id="<?php echo esc_attr( $field['id'] ); ?>_field_value"
                            name="<?php echo esc_attr( $field['name'] ); ?>[field_value]"
                            value="<?php echo esc_attr( $field_value ?? '' ); ?>"/>
				</td>
			</tr>
		</table>
		<?php
	}
}
