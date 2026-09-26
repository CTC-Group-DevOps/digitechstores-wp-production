<?php
/**
 * Seo social tab template.
 *
 * @var array $seo_settings
 * @var WP_Post | null $post
 * @var int $post_id
 * @var Vc_Post_Seo $vc_post_seo
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div id="vc_ui-seo-social">
	<?php
	foreach ( $vc_post_seo->get_social_network_list() as $network_slug => $network_name ) {
		$slug = 'social-image-' . $network_slug;
		$image_id = (int) empty( $seo_settings[ $slug ] ) ? '' : $seo_settings[ $slug ];
		?>
		<div class="vc_row vc_seo-social-block">
			<div class="vc_col-sm-12 vc_column">
				<?php
				vc_include_template(
					'editors/popups/seo/seo-social-preview.php',
					[
						'seo_settings' => $seo_settings,
						'post' => $post,
						'post_id' => $post_id,
						'vc_post_seo' => $vc_post_seo,
						'network_slug' => $network_slug,
						'network_name' => $network_name,
						'image_id' => $image_id,
					]
				);
				?>
			</div>
			<div class="vc_col-sm-12 vc_column wpb_el_type_attach_image">
				<div class="wpb_element_label"><?php esc_html_e( 'Image', 'js_composer' ); ?></div>
				<div class="edit_form_line" data-social-net-preview-slug="<?php echo esc_attr( 'wpb-' . $network_slug . '-preview' ); ?>">
					<?php
					WPB_Form_Field_Attach_Image::render(
						[
							'param_value' => $image_id,
							'image_id'    => $image_id,
							'thumb_src' => wpb_get_image_thumb( $image_id, 'medium' ),
							'name' => 'social-image-' . $network_slug,
							'classes' => wpbakery()->editForm()->get_value_control_classes( 'social-image-' . $network_slug, 'attach_image' ),
						]
					);
					?>
				</div>
			</div>
			<div class="vc_col-sm-12 vc_column">
				<?php
				$title_name = 'social-title-' . $network_slug;
				$title_id = $title_name;
				$value = empty( $seo_settings[ $title_name ] ) ? '' : $seo_settings[ $title_name ];
				?>
				<label for="<?php echo esc_attr( $title_id ); ?>" class="wpb_element_label"><?php esc_html_e( 'Social title', 'js_composer' ); ?></label>
				<div class="edit_form_line">
					<?php
					if ( vc_modules_manager()->is_module_on( 'vc-ai' ) ) {
						wpb_add_ai_icon_to_text_field( 'seo_title', $title_id );
					}
					WPB_Form_Field_Textfield::render(
						[
							'id' => $title_id,
							'classes' => 'vc_social-title-field',
							'name' => $title_name,
							'value' => $value,
						]
					);
					?>
				</div>
			</div>
			<div class="vc_col-sm-12 vc_column">
				<?php
				$description_name = 'social-description-' . $network_slug;
				$description_id = $description_name;

				$value = empty( $seo_settings[ $description_name ] ) ? '' : $seo_settings[ $description_name ];
				?>
				<label for="<?php echo esc_attr( $description_id ); ?>" class="wpb_element_label"><?php esc_html_e( 'Social description', 'js_composer' ); ?></label>
				<div class="edit_form_line">
					<?php
					if ( vc_modules_manager()->is_module_on( 'vc-ai' ) ) {
						wpb_add_ai_icon_to_text_field( 'seo_meta_description_social', $description_id );
					}
					?>
					<?php
					WPB_Form_Field_Textarea::render( [
						'id'        => $description_id,
						'name'      => $description_name,
						'value'     => $value,
						'class'     => 'vc_social-description-field',
						'maxlength' => '255',
					] );
					?>
				</div>
			</div>
		</div>
		<?php
	}
	?>
</div>
