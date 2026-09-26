<?php
/**
 * Option dropdown form field template.
 *
 * @param array $option_data
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<option
	<?php
	if ( isset( $option_data['class'] ) ) {
		echo ' class="' . esc_attr( $option_data['class'] ) . '"';
	}
	if ( isset( $option_data['value'] ) ) {
		echo ' value="' . esc_attr( $option_data['value'] ) . '"';
	}
	if ( ! empty( $option_data['selected'] ) ) {
		echo ' selected';
	}
	if ( ! empty( $option_data['default-font-style'] ) ) {
		echo ' default[font_style]="' . esc_attr( $option_data['default-font-style'] ) . '"';
	}
	if ( ! empty( $option_data['underscore_code'] ) ) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $option_data['underscore_code'];
	}
	if ( ! empty( $option_data['custom-selector'] ) ) {
		echo ' data-custom-selector="' . esc_attr( $option_data['custom-selector'] ) . '"';
	}
	if ( ! empty( $option_data['font_data'] ) ) {
		foreach ( $option_data['font_data'] as $data_name => $font_data ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ' data[' . esc_attr( $data_name ) . ']="' . esc_attr( $font_data ) . '"';
		}
	}
	if ( ! empty( $option_data['data_attributes'] ) ) {
		foreach ( $option_data['data_attributes'] as $data_name => $data_value ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ' data-' . esc_attr( $data_name ) . '="' . esc_attr( $data_value ) . '"';
		}
	}

	if ( ! empty( $option_data['is_disabled'] ) ) {
		echo ' disabled';
	}

	echo '>';

	if ( isset( $option_data['label'] ) ) {
		echo esc_html( $option_data['label'] );
	}

	echo '</option>';
	?>
