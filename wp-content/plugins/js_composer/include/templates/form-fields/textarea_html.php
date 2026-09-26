<?php
/**
 * Textarea HTML form field template.
 *
 * @var string $id             Field id attribute.
 * @var string $name           Field name attribute.
 * @var string $value          Field value.
 * @var string $editor_class   CSS classes for the editor.
 * @var bool   $media_buttons  Whether to show media buttons.
 * @var bool   $wpautop        Whether to use wpautop.
 * @var string $class          CSS classes for hidden input.
 * @var string $data_attr_string Pre-built data attributes string.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( function_exists( 'wp_editor' ) ) {
	$default_content = is_string( $value ) ? $value : '';

	// Capture wp_editor output.
	ob_start();
	wp_editor( $default_content, $id, [
		'editor_class' => $editor_class,
		'media_buttons' => $media_buttons,
		'wpautop' => $wpautop,
	] );
	$editor_output = ob_get_contents();
	ob_end_clean();

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_editor output is safe
	echo $editor_output;

	// Hidden input to store the actual value.
	?>
	<input type="hidden"
		name="<?php echo esc_attr( $name ); ?>"
		<?php echo $class ? 'class="' . esc_attr( $class ) . '"' : ''; ?>
		value="<?php echo esc_attr( $default_content ); ?>"
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped during string build.
		echo $data_attr_string;
		?>
	/>
	<?php
}
