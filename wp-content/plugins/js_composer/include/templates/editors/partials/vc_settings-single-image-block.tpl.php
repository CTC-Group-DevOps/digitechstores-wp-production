<?php
/**
 * Template for a single image attachment block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$custom_tag = 'script';
?>
<<?php echo esc_attr( $custom_tag ); ?> type="text/html" id="vc_settings-single-image-block">
	<?php
	vc_include_template('form-fields/attach_image/attach_image_inner_block.php', [
		'is_template' => true,
		'is_link_icon' => false,
		'link' => [
			'title' => '',
			'target' => '',
			'rel' => '',
		],
	] )
	?>
</<?php echo esc_attr( $custom_tag ); ?>>
