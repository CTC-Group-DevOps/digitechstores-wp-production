<?php
/**
 * General tab template.
 *
 * @var array $seo_settings
 * @var WP_Post | null $post
 * @var int $post_id
 * @var Vc_Post_Seo $vc_post_seo
 * @var string $permalink_structure
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div id="vc_ui-seo-general">
	<div class="vc_row">
		<div class="vc_col-sm-12 vc_column">
			<label for="vc_focus-keyphrase-field" class="wpb_element_label"><?php esc_html_e( 'Focus keyphrase', 'js_composer' ); ?></label>
			<div class="edit_form_line">
				<?php
				WPB_Form_Field_Textfield::render(
					[
						'id'      => 'vc_focus-keyphrase-field',
						'name'    => 'focus-keyphrase',
						'value'   => empty( $seo_settings['focus-keyphrase'] ) ? '' : $seo_settings['focus-keyphrase'],
					]
				);
				?>
			</div>
		</div>
		<div class="vc_col-sm-12 vc_column">
			<fieldset>
				<legend class="wpb_element_label"><?php esc_html_e( 'Preview as:', 'js_composer' ); ?></legend>
				<div class="vc-preview-radio">
					<?php
					WPB_Form_Field_Radio::render( [
						'id'      => 'mobile-result',
						'name'    => 'preview-as',
						'value'   => 'mobile',
						'label'   => esc_html__( 'Mobile result', 'js_composer' ),
						'checked' => true,
					] );
					WPB_Form_Field_Radio::render( [
						'id'      => 'desktop-result',
						'name'    => 'preview-as',
						'value'   => 'desktop',
						'label'   => esc_html__( 'Desktop result', 'js_composer' ),
						'checked' => false,
					] );
					?>
				</div>
			</fieldset>
			<?php
			vc_include_template(
				'editors/popups/seo/seo-general-preview.php',
				[
					'seo_settings' => $seo_settings,
					'post' => $post,
					'post_id' => $post_id,
					'vc_post_seo' => $vc_post_seo,
					'permalink_structure' => $permalink_structure,
				]
			);
			?>
		</div>
		<div class="vc_col-sm-12 vc_column">
			<label for="vc_seo-title-field" class="wpb_element_label"><?php esc_html_e( 'SEO title', 'js_composer' ); ?></label>
			<div class="edit_form_line">
				<?php
				if ( vc_modules_manager()->is_module_on( 'vc-ai' ) ) {
					wpb_add_ai_icon_to_text_field( 'seo_title', 'vc_seo-title-field' );
				}
				WPB_Form_Field_Textfield::render(
					[
						'id' => 'vc_seo-title-field',
						'name' => 'title',
						'value' => empty( $seo_settings['title'] ) ? '' : $seo_settings['title'],
						'data_attr_list' => [ 'preview' => 'vc_seo-title' ],
					]
				);
				?>
			</div>
		</div>
		<div class="vc_col-sm-12 vc_column">
			<label for="vc_seo-slug-field" class="wpb_element_label"><?php esc_html_e( 'Slug', 'js_composer' ); ?></label>
			<div class="edit_form_line">
				<?php
				WPB_Form_Field_Textfield::render(
					[
						'id' => 'vc_seo-slug-field',
						'name' => 'slug',
						'value' => empty( $permalink_structure ) ? '' : get_post_field( 'post_name', $post ),
						'data_attr_list' => [ 'preview' => 'vc_seo-slug' ],
					]
				);
				?>
			</div>
		</div>
		<div class="vc_col-sm-12 vc_column">
			<label for="vc_seo-description-field" class="wpb_element_label"><?php esc_html_e( 'Meta description', 'js_composer' ); ?></label>
			<div class="edit_form_line">
				<?php
				if ( vc_modules_manager()->is_module_on( 'vc-ai' ) ) {
					wpb_add_ai_icon_to_text_field( 'seo_meta_description', 'vc_seo-description-field' );
				}
				?>
	<?php
				WPB_Form_Field_Textarea::render( [
					'id'              => 'vc_seo-description-field',
					'name'            => 'description',
					'value'           => empty( $seo_settings['description'] ) ? '' : $seo_settings['description'],
					'data_attributes' => [
						'preview' => 'vc_seo-description',
					],
				] );
				?>
			</div>
		</div>
	</div>
</div>

