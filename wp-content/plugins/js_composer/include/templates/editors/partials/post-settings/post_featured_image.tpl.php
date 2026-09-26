<?php
/**
 * Featured image control page settings panel template.
 *
 * @since 8.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
global $post;
$featured_image = get_post_thumbnail_id( $post->ID );
$featured_image = strval( $featured_image );
if ( '0' === $featured_image ) {
	// set to empty string to prevent default image from being selected in media library.
	$featured_image = '';
}
?>

<div class="vc_col-sm-12 vc_column wpb_el_type_attach_image vc_wrapper-param-type-attach_image" id="vc_settings-featured-image">
	<div class="wpb_settings-title">
		<label for="vc_featured_image" class="wpb_element_label"><?php esc_html_e( 'Featured image', 'js_composer' ); ?></label>
	</div>
	<div class="edit_form_line">
		<?php
		$param_value = wpb_removeNotExistingImgIDs( $featured_image );

		WPB_Form_Field_Attach_Image::render(
			[
				'param_value' => $param_value,
				'image_id'    => $param_value,
				'thumb_src' => wpb_get_image_thumb( $featured_image, 'medium' ),
				'name' => 'featured_image',
				'id' => 'vc_featured_image',
				'classes' => wpbakery()->editForm()->get_value_control_classes( 'featured_image', 'attach_image' ),
			]
		);
		?>
	</div>
</div>
