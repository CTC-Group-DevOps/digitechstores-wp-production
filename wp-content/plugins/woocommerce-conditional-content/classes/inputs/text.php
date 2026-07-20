<?php

class WC_Conditional_Content_Input_Text extends WC_Conditional_Content_Input_Base {

	public function __construct() {
		// vars
		$this->type = 'Text';

		$this->defaults = [
			'default_value' => '',
			'class'         => '',
			'placeholder'   => ''
		];
	}

	public function render( $field, $value = null ): void {
		$field = array_merge( $this->defaults, $field );
		if ( ! isset( $field['id'] ) ) {
			$field['id'] = sanitize_title( $field['id'] );
		}

		?>
		<input
			name="<?php echo esc_attr( $field['name'] ); ?>"
			type="text"
			id="<?php echo esc_attr( $field['id'] ); ?>"
			class="<?php echo esc_attr( $field['class'] ); ?>"
			placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
		/>
		<?php
	}
}
