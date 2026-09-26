<?php
/**
 * Attach image form field template.
 *
 * @var string $param_value
 * @var string $image_id
 * @var string $classes
 * @var string $id
 * @var string $name
 * @var string $thumb_src
 * @var string $label_upload
 * @var string $label_edit
 * @var string $label_remove
 * @var bool $is_link_icon
 * @var array $link
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
vc_include_template('form-fields/attach_image/input.php', [
	'value' => $param_value,
	'classes' => $classes,
	'id' => $id,
	'name' => $name,
] );
?>

<div class="gallery_widget_attached_image_wrapper">
	<?php
	if ( $thumb_src ) :
		vc_include_template( 'form-fields/attach_image/attach_image_inner_block.php', [
			'is_template'  => false,
			'thumb_src'    => $thumb_src,
			'image_id'     => $image_id,
			'label_upload' => $label_upload,
			'label_edit'   => $label_edit,
			'label_remove' => $label_remove,
			'is_link_icon' => $is_link_icon,
			'link'         => $link,
			'name'         => $name,
			'id'           => $id,
		] );
	endif;
	?>
</div>
<button class="gallery_widget_add_images" type="button" use-single="true" data-is-link-icon="<?php echo $is_link_icon ? 'true' : 'false'; ?>" title="<?php esc_attr_e( 'Add image', 'js_composer' ); ?>" aria-label="<?php esc_attr_e( 'Add image', 'js_composer' ); ?>" <?php echo '' === $param_value ? '' : ' style="display: none;"'; ?>>
	<i class="vc-composer-icon vc-c-image"></i><?php esc_html_e( 'Add image', 'js_composer' ); ?>
</button>
