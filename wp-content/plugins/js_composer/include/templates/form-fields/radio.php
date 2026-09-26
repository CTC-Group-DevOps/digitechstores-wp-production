<?php
/**
 * Radio form field template.
 *
 * @var string $id                Field id attribute.
 * @var string $name              Field name attribute.
 * @var string $value             Field value.
 * @var string $label             Label text.
 * @var bool   $checked           Whether radio is checked.
 * @var string $class             Additional CSS classes for item wrapper.
 * @var string $wrapper_class     Additional CSS classes for outer wrapper.
 * @var string $input_attr_class  Pre-built input class attribute string (e.g. ' class="..."').
 * @var string $description       Optional description text.
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
<div class="wpb_radio-item <?php echo esc_attr( $class ); ?> <?php echo esc_attr( $wrapper_class ); ?>">
	<input type="radio"
		<?php echo $id ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
		<?php echo $name ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by caller.
		echo $input_attr_class;
		?>
		value="<?php echo esc_attr( $value ); ?>"
		<?php checked( $checked ); ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped during string build.
		echo $data_attr_string;
		?>
	/>
	<label class="wpb_radio-label" <?php echo $id ? 'for="' . esc_attr( $id ) . '"' : ''; ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped during string build.
		echo $label_attr_string;
		?>
	>
		<span class="wpb_radio-visual"></span>
		<span class="wpb_radio-label-text"><?php echo esc_html( $label ); ?></span>
		<?php
		if ( $description ) :
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- description contains pre-escaped HTML from templates.
			echo $description;
		endif;
		?>
	</label>
</div>
