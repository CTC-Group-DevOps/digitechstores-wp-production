<?php
/**
 * Attach image hidden input form field template.
 *
 * @var string $value
 * @var string $classes
 * @var string $id
 * @var string $name
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<input type="hidden"
		class="<?php echo esc_attr( $classes ); ?> gallery_widget_attached_images_ids"
		<?php
		if ( ! empty( $id ) ) {
			?>
			id="<?php echo esc_attr( $id ); ?>"
			<?php
		}
		if ( ! empty( $name ) ) {
			?>
			name="<?php echo esc_attr( $name ); ?>"
			<?php
		}
		if ( ! empty( $value ) ) {
			?>
			value="<?php echo esc_attr( $value ); ?>"
			<?php
		}
		?>
>
