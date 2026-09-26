<?php
/**
 * Button template for attach_image form field.
 *
 * @var string $extra_classes Additional CSS classes beyond gallery_widget_control.
 * @var string $data_action   Value for data-action attribute.
 * @var string $title         Title and aria-label text.
 * @var string $icon_class    Icon CSS class.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<button class="gallery_widget_control <?php echo esc_attr( $extra_classes ); ?>" data-action="<?php echo esc_attr( $data_action ); ?>" title="<?php echo esc_attr( $title ); ?>" aria-label="<?php echo esc_attr( $title ); ?>" type="button">
	<i class="vc-composer-icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
</button>
