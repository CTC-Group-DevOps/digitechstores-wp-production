<?php
/**
 * Prompt template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<form class="vc_ui-prompt vc_ui-prompt-templates">
	<div class="vc_ui-prompt-title">
		<label for="prompt_templates_title" class="wpb_element_label"><?php esc_html_e( 'Save template', 'js_composer' ); ?></label>
		<?php
			$template_title_info = vc_get_template( 'editors/partials/param-info.tpl.php', [ 'description' => esc_html__( 'Enter element template title.', 'js_composer' ) ] );
			// phpcs:ignore
			if ( is_string( $template_title_info ) ) { echo $template_title_info; }
		?>
	</div>
	<div class="vc_ui-prompt-content">
		<div class="vc_ui-prompt-column">
			<div class="wpb_el_type_textfield vc_wrapper-param-type-textfield vc_properties-list">
				<div class="edit_form_line">
					<?php
					WPB_Form_Field_Textfield::render(
						[
							'id'   => 'prompt_templates_title',
							'name' => 'title',
							'classes' => 'wpb_vc_param_value h4 textfield',
						]
					);
					?>
				</div>
			</div>
		</div>
		<div class="vc_ui-prompt-column">
			<button type="submit"
				class="vc_general vc_ui-button vc_ui-button-size-md vc_ui-button-action vc_ui-button-shape-rounded vc_preset-save-btn" id="vc_ui-save-templates-btn"><?php esc_html_e( 'Save', 'js_composer' ); ?></button>
		</div>
	</div>
</form>
