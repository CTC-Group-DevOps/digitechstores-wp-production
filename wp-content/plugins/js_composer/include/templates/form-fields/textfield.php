<?php
/**
 * Toggle form field template.
 *
 * @var string $id
 * @var string $classes
 * @var string $name
 * @var string $value
 * @var string $placeholder
 * @var string $value_type
 * @var string $underscore_code
 * @var string $style
 * @var array $data_attr_list
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<input type="text" class="wpb-form-input <?php echo esc_attr( $classes ); ?>"
<?php
if ( '' !== $value ) {
	echo ' value="' . esc_attr( $value ) . '"';
}
if ( '' !== $id ) {
	echo ' id="' . esc_attr( $id ) . '"';
}
if ( '' !== $name ) {
	echo ' name="' . esc_attr( $name ) . '"';
}
if ( '' !== $placeholder ) {
	echo ' placeholder="' . esc_attr( $placeholder ) . '"';
}
if ( '' !== $value_type ) {
	echo ' data-value-type="' . esc_attr( $value_type ) . '"';
}
if ( '' !== $style ) {
	echo ' style="' . esc_attr( $style ) . '"';
}
if ( '' !== $underscore_code ) {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $underscore_code;
}
foreach ( $data_attr_list as $data_attr_name => $data_attr_value ) {
	echo ' data-' . esc_attr( $data_attr_name ) . '="' . esc_attr( $data_attr_value ) . '"';
}
?>
>
