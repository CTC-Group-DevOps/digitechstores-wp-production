<?php
/**
 * Checkbox form field template.
 *
 * @var string $id                Field id attribute.
 * @var string $name              Field name attribute.
 * @var string $value             Field value.
 * @var string $label             Label text.
 * @var bool   $checked           Whether checkbox is checked.
 * @var bool   $disabled          Whether checkbox is disabled.
 * @var string $class             Additional CSS classes for label.
 * @var string $input_attr_class  Pre-built input class attribute string (e.g. ' class="..."').
 * @var string $label_attr_string Pre-built label data attributes string.
 * @var string $data_attr_string  Pre-built input data attributes string.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<label class="vc_checkbox-label <?php echo esc_attr( $class ); ?>" <?php echo $id ? 'for="' . esc_attr( $id ) . '"' : ''; ?>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped during string build.
	echo $label_attr_string;
	?>
>
	<input
		type="checkbox"
		<?php echo $id ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
		<?php echo $name ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by caller.
		echo $input_attr_class;
		?>
		value="<?php echo esc_attr( $value ); ?>"
		<?php checked( $checked ); ?>
		<?php disabled( $disabled ); ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped during string build.
		echo $data_attr_string;
		?>
	><?php echo esc_html( $label ); ?>
</label>
