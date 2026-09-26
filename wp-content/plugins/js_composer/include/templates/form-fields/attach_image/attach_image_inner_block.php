<?php
/**
 * Single image block for attach_image form field template.
 *
 * @var string $thumb_src
 * @var string $image_id
 * @var string $label_upload
 * @var string $label_edit
 * @var string $label_remove
 * @var bool $is_link_icon
 * @var bool $is_template
 * @var string|null $name
 * @var string|null $id
 * @var array $link
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div class="gallery_widget_attached_image"<?php echo ! $is_template ? ' style="background-image: url(' . esc_url( $thumb_src ) . ');"' : ''; ?>>
	<button class="gallery_widget_attached_image_preview" type="button" style="background-image: url('<?php echo $is_template ? '{{ url }}' : esc_url( $thumb_src ); ?>');" data-image-id="<?php echo $is_template ? '{{ id }}' : esc_attr( $image_id ); ?>" aria-hidden="true"></button>
	<div class="gallery_widget_attached_image_actions <?php echo $is_template ? '{{ is_link_icon }}' : ''; ?> <?php echo $is_link_icon ? ' vc-linked-image' : ''; ?>">
		<?php
		vc_include_template( 'form-fields/attach_image/icon_button.php', [
			'extra_classes' => 'gallery_widget_image_upload',
			'data_action'   => 'upload',
			'title'         => $is_template ? '{{ window.i18nLocale.upload }}' : $label_upload,
			'icon_class'    => 'vc-c-upload',
		] );
		vc_include_template( 'form-fields/attach_image/icon_button.php', [
			'extra_classes' => 'gallery_widget_image_link',
			'data_action'   => 'link',
			'title'         => __( 'Link', 'js_composer' ),
			'icon_class'    => 'vc-c-linked',
		] );
		vc_include_template( 'form-fields/attach_image/icon_button.php', [
			'extra_classes' => 'gallery_widget_image_edit',
			'data_action'   => 'edit',
			'title'         => $is_template ? '{{ window.i18nLocale.edit }}' : $label_edit,
			'icon_class'    => 'vc-c-edit',
		] );
		vc_include_template( 'form-fields/attach_image/icon_button.php', [
			'extra_classes' => 'gallery_widget_image_remove',
			'data_action'   => 'remove',
			'title'         => $is_template ? '{{ window.i18nLocale.remove }}' : $label_remove,
			'icon_class'    => 'vc-c-token-close',
		] );
		?>
	</div>
	<?php if ( $is_link_icon || $is_template ) : ?>
		<?php
		vc_include_template( 'form-fields/link/modal.php', [
			'link'     => $link,
			'name'     => $name ?? '',
			'settings' => [
				'is_title' => false,
			],
			'id'       => $id ?? '',
		] );
		?>
	<?php endif; ?>
</div>
