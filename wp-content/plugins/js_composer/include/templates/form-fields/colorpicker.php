<?php
/**
 * Colorpicker form field template.
 *
 * @var string $id
 * @var string $classes
 * @var string $name
 * @var string $value
 * @var array $data_attributes
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="color-group">
	<div class="wpb-color-picker"></div>
	<input
			type="text"
			class="vc_color-control vc_ui-hidden <?php echo esc_attr( $classes ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			<?php
			if ( '' !== $id ) {
				echo ' id="' . esc_attr( $id ) . '"';
			}
			foreach ( $data_attributes as $data_attr_name => $data_attr_value ) {
				echo ' data-' . esc_attr( $data_attr_name ) . '="' . esc_attr( $data_attr_value ) . '"';
			}
			?>
	>
</div>
