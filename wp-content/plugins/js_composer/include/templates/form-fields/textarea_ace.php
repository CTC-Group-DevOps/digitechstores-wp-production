<?php
/**
 * Textarea ace form field template.
 *
 * @var string $id
 * @var string $classes
 * @var string $decoded_value
 * @var string|null $data_attr_string
 * @var string $style
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<pre
	id="<?php echo esc_attr( $id ); ?>"
	class="<?php echo esc_attr( $classes ); ?> custom_code"
	<?php
	if ( ! empty( $style ) ) {
		?>
		style="<?php echo esc_attr( $style ); ?>"
		<?php
	}
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped during string build.
	echo $data_attr_string ?? '';
	?>
><?php echo esc_textarea( $decoded_value ); ?></pre>
