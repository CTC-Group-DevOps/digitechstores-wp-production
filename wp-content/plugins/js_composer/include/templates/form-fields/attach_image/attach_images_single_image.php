<?php
/**
 * Single image for attach_images form field template.
 *
 * @var string $label_remove
 * @var string $image
 * @var string $thumb_src
 * @var bool $is_template
 * @var bool $is_link_icon
 * @var array|null $link
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<li class="added gallery_widget_attached_image" rel="<?php echo $is_template ? '{{ id }}' : esc_attr( $image ); ?>">
	<button class="gallery_widget_attached_image_preview" type="button" style="background-image: url(<?php echo $is_template ? '{{ url }}' : esc_url( $thumb_src ); ?>);" aria-hidden="true"></button>
	<div class="gallery_widget_attached_image_actions <?php echo $is_template ? '{{ is_link_icon }}' : ''; ?><?php echo ! $is_template && $is_link_icon ? ' vc-linked-image' : ''; ?>">
		<?php
		vc_include_template( 'form-fields/attach_image/icon_button.php', [
			'extra_classes' => 'gallery_widget_image_link',
			'data_action'   => 'link',
			'title'         => __( 'Link', 'js_composer' ),
			'icon_class'    => 'vc-c-linked',
		] );
		vc_include_template( 'form-fields/attach_image/icon_button.php', [
			'extra_classes' => 'gallery_widget_image_remove vc_icon-remove',
			'data_action'   => 'remove',
			'title'         => $is_template ? '{{ window.i18nLocale.remove }}' : $label_remove,
			'icon_class'    => 'vc-c-token-close',
		] );
		?>
	</div>
	<?php if ( $is_link_icon || $is_template ) : ?>
		<?php
		vc_include_template( 'form-fields/link/modal.php', [
			'link'     => $link ?? [],
			'name'     => $name ?? '',
			'id'       => $is_template ? '{{ id }}' : $image,
			'settings' => [
				'is_title' => false,
			],
		] );
		?>
	<?php endif; ?>
</li>
