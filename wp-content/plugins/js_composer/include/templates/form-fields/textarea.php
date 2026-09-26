<?php
/**
 * Textarea form field template.
 *
 * @var string $id               Field id attribute.
 * @var string $name             Field name attribute.
 * @var string $value            Field value.
 * @var string $placeholder      Placeholder text.
 * @var string $rows             Number of visible text lines.
 * @var string $maxlength        Maximum number of characters.
 * @var bool   $disabled         Whether textarea is disabled.
 * @var string $value_type       Value type (e.g. 'html').
 * @var string $class            CSS classes for textarea.
 * @var string $style            Inline styles for textarea.
 * @var string $data_attr_string Pre-built data attributes string.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<textarea
	name="<?php echo esc_attr( $name ); ?>"
	<?php echo $id ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
	class="wpb-form-textarea<?php echo $class ? ' ' . esc_attr( $class ) : ''; ?>"
	<?php echo $style ? 'style="' . esc_attr( $style ) . '"' : ''; ?>
	<?php echo $rows ? 'rows="' . esc_attr( $rows ) . '"' : ''; ?>
	<?php echo $maxlength ? 'maxlength="' . esc_attr( $maxlength ) . '"' : ''; ?>
	<?php disabled( $disabled ); ?>
	<?php echo $value_type ? 'data-value-type="' . esc_attr( $value_type ) . '"' : ''; ?>
	<?php echo '' !== $placeholder ? 'placeholder="' . esc_attr( $placeholder ) . '"' : ''; ?>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped during string build.
	echo $data_attr_string;
	?>
><?php echo esc_textarea( (string) $value ); ?></textarea>
