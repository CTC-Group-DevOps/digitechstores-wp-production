<?php
/**
 * Custom JavaScript tab template.
 *
 * @var array $page_settings_data
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="vc_col-sm-12 vc_column">
	<div class="wpb_settings-title">
		<div class="wpb_element_label">
			<?php esc_html_e( 'Custom JavaScript in <head>', 'js_composer' ); ?>
		</div>
		<?php
		vc_include_template(
			$page_settings_data['js_head_info_template'],
			[ 'description' => $page_settings_data['js_head_info_description'] ]
		);
		?>
	</div>
	<div class="edit_form_line">
		<div class="vc_ui-settings-text-wrapper">
			<p class="wpb-code-editor-tag">&lt;script&gt;</p>
			<?php
			if ( vc_modules_manager()->is_module_on( 'vc-ai' ) ) {
				wpb_add_ai_icon_to_code_field( 'custom_js', 'wpb_js_header_editor' );
			}
			?>
		</div>
		<?php
		WPB_Form_Field_Textarea_Ace::render([
			'id' => 'wpb_js_header_editor',
			'classes' => 'wpb_content_element' . $page_settings_data['can_unfiltered_html_cap'] ? '' : 'wpb_missing_unfiltered_html',
			'decoded_value' => $page_settings_data['can_unfiltered_html_cap'] ? $page_settings_data['custom_js_header'] : wpbakery()->getEditorsLocale()['unfiltered_html_access'],
			'data_attributes' => [
				'code-type' => 'javascript',
				'ace-location' => 'page-js',
			],
		]);
		?>
		<p class="wpb-code-editor-tag">&lt;/script&gt;</p>
	</div>
</div>
<div class="vc_col-sm-12 vc_column">
	<div class="wpb_settings-title">
		<div class="wpb_element_label">
			<?php esc_html_e( 'Custom JavaScript before </body>', 'js_composer' ); ?>
		</div>
		<?php
		vc_include_template(
			$page_settings_data['js_body_info_template'],
			[ 'description' => $page_settings_data['js_body_info_description'] ]
		);
		?>
	</div>
	<div class="edit_form_line">
		<div class="vc_ui-settings-text-wrapper">
			<p class="wpb-code-editor-tag">&lt;script&gt;</p>
			<?php
			if ( vc_modules_manager()->is_module_on( 'vc-ai' ) ) {
				wpb_add_ai_icon_to_code_field( 'custom_js', 'wpb_js_footer_editor' );
			}
			?>
		</div>
		<?php
		WPB_Form_Field_Textarea_Ace::render([
			'id' => 'wpb_js_footer_editor',
			'classes' => 'wpb_content_element' . ( $page_settings_data['can_unfiltered_html_cap'] ? '' : 'wpb_missing_unfiltered_html' ),
			'decoded_value' => $page_settings_data['can_unfiltered_html_cap'] ? $page_settings_data['custom_js_footer'] : wpbakery()->getEditorsLocale()['unfiltered_html_access'],
			'data_attributes' => [
				'code-type' => 'javascript',
				'ace-location' => 'page-js',
			],
		]);
		?>
		<p class="wpb-code-editor-tag">&lt;/script&gt;</p>
	</div>
</div>

