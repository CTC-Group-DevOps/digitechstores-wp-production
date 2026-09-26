<?php
/**
 * Toggle form field template.
 *
 * @var string $id
 * @var string $classes
 * @var string $name
 * @var bool $is_checked
 * @var string $checked_value
 * @var string $title
 * @var string $container_classes
 * @var array $data_attributes Data attributes array.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="wpb_toggle-switch <?php echo esc_attr( $container_classes ); ?>">
	<input type="checkbox"
			id="<?php echo esc_attr( $id ); ?>"
			class="<?php echo esc_attr( $classes ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			value="<?php echo esc_attr( $checked_value ); ?>"
			role="switch"
			aria-checked="<?php echo $is_checked ? 'true' : 'false'; ?>" <?php checked( $is_checked ); ?>
			<?php
			if ( ! empty( $data_attributes ) && is_array( $data_attributes ) ) {
				foreach ( $data_attributes as $key => $val ) {
					echo ' data-' . esc_attr( $key ) . '="' . esc_attr( $val ) . '"';
				}
			}
			?>
	>
	<label class="wpb_toggle-track" for="<?php echo esc_attr( $id ); ?>">
		<span class="wpb_toggle-thumb"></span>
	</label>
	<?php
	if ( $title ) {
		?>
		<div class="wpb_element_label">
			<?php echo esc_html( $title ); ?>
		</div>
		<?php
	}
	?>
</div>
