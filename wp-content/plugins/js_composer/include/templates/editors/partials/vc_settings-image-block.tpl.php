<?php
/**
 * Settings image block template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$custom_tag = 'script';
?>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_settings-image-block">
	<?php
	vc_include_template('form-fields/attach_image/attach_images_single_image.php', [
		'is_template' => true,
		'is_link_icon' => false,
	] )
	?>
</<?php echo esc_attr( $custom_tag ); ?>>
