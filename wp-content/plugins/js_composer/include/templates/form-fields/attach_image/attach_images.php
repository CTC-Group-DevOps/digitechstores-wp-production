<?php
/**
 * Attach images form field template.
 *
 * @var string $value
 * @var string $image_id_list
 * @var string $classes
 * @var string $id
 * @var string $name
 * @var array $images
 * @var string $label_reorder
 * @var string $label_remove
 * @var bool $is_link_icon
 * @var false|string $images_output
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

vc_include_template('form-fields/attach_image/input.php', [
	'value' => $value,
	'classes' => $classes,
	'id' => $id,
	'name' => $name,
] );
?>

<div class="gallery_widget_attached_images">
	<ul class="gallery_widget_attached_images_list">
		<?php
		if ( '' !== $image_id_list ) :
			if ( false === $images_output ) {
				echo vc_field_attached_images( $images ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo $images_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		endif;
		?>
		<li>
			<button class="gallery_widget_add_images" type="button" data-is-link-icon="<?php echo $is_link_icon ? 'true' : 'false'; ?>" title="<?php esc_attr_e( 'Add images', 'js_composer' ); ?>" aria-label="<?php esc_attr_e( 'Add images', 'js_composer' ); ?>">
				<i class="vc-composer-icon vc-c-images"></i><?php esc_html_e( 'Add images', 'js_composer' ); ?>
			</button>
		</li>
	</ul>
</div>
