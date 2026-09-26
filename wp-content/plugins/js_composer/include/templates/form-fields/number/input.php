<?php
/**
 * Template for input element param number.
 *
 * @since 9.0
 * @var string $value
 * @var string $param_id
 * @var string $id
 * @var string $classes
 * @var string $name
 * @var string $placeholder
 * @var string $min
 * @var string $max
 * @var string $step
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<input
	type="number"
	<?php
	if ( '' !== $value ) {
		echo ' value="' . esc_attr( $value ) . '"';
	}
	if ( '' !== $id ) {
		echo ' id="' . esc_attr( $id ) . '"';
	}
	if ( '' !== $classes ) {
		echo ' class="wpb-form-input ' . esc_attr( $classes ) . '"';
	}
	if ( '' !== $name ) {
		echo ' name="' . esc_attr( $name ) . '"';
	}
	if ( '' !== $placeholder ) {
		echo ' placeholder="' . esc_attr( $placeholder ) . '"';
	}
	if ( '' !== $min ) {
		echo ' min="' . esc_attr( $min ) . '"';
	}
	if ( '' !== $max ) {
		echo ' max="' . esc_attr( $max ) . '"';
	}
	if ( '' !== $step ) {
		echo ' step="' . esc_attr( $step ) . '"';
	}
	?>
>
