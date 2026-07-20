<?php

class WC_Conditional_Content_Input_Select extends WC_Conditional_Content_Input_Base {

	public function __construct() {
		$this->type = 'Select';

		$this->defaults = array(
			'multiple'      => 0,
			'allow_null'    => 0,
			'choices'       => array(),
			'default_value' => '',
			'class'         => '',
		);
	}

	public function render( $field, $value = null ): void {

		$field          = array_merge( $this->defaults, $field );
		$field['value'] = $value;
		$optgroup       = false;

		// determine if choices are grouped (2 levels of array).
		if ( is_array( $field['choices'] ) ) {
			foreach ( $field['choices'] as $k => $v ) {
				if ( is_array( $v ) ) {
					$optgroup = true;
				}
			}
		}

		// value must be array.
		if ( ! is_array( $field['value'] ) ) {
			// perhaps this is a default value with new lines in it?
			if ( str_contains( $field['value'], "\n" ) ) {
				// found multiple lines, explode it.
				$field['value'] = explode( "\n", $field['value'] );
			} else {
				$field['value'] = array( $field['value'] );
			}
		}

		// trim value.
		$field['value'] = array_map( 'trim', $field['value'] );
		$field_name = $field['name'];

		// If the field is a Backbone template field, do not escape the name.  Otherwise, escape it for esc_attr.
		// This required because the Backbone template is rendered from metabox-rules-rule-template.php.
		if ( strpos( $field_name, '<%= groupId %>' ) !== false ) {
			$field['name'] = $field_name;
		} else {
			$field['name'] = esc_attr( $field_name );
		}



		if ( $field['multiple'] ) {
			$field['name'] .= '[]';
			// Tell PHP to ignore the next line for phpcs. This is because the field['name'] is already escaped, or not depending on the context.
			// phpcs:disable
			echo '<select multiple="multiple" size="5" id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . ( $field['name'] ) . '">';
		} else {
			// Tell PHP to ignore the next line for phpcs. This is because the field['name'] is already escaped, or not depending on the context.
			// phpcs:disable
			echo '<select id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . ( $field['name'] ) . '">';
		}

		if ( $field['allow_null'] ) {
			echo '<option value="null"> - Select - </option>';
		}

		// loop through values and add them as options
		if ( is_array( $field['choices'] ) ) {
			foreach ( $field['choices'] as $key => $value ) {
				if ( $optgroup ) {
					// this select is grouped with optgroup
					if ( $key !== '' ) {
						echo '<optgroup label="' . esc_attr( $key ) . '">';
					}

					if ( is_array( $value ) ) {
						foreach ( $value as $id => $label ) {
							$selected = in_array(  $id , $field['value'] ) ? 'selected="selected"' : '';
							echo '<option value="' . $id . '" ' . $selected . '>' . $label . '</option>';
						}
					}

					if ( $key !== '' ) {
						echo '</optgroup>';
					}
				} else {
					$selected = in_array( $key, $field['value'] ) ? 'selected="selected"' : '';
					echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
				}
			}
		}

		echo '</select>';
	}
}
