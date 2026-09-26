<?php
/**
 * Prompt presets template.
 *
 * @var string $info
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<form class="vc_ui-prompt vc_ui-prompt-presets">
	<div class="vc_ui-prompt-title">
		<label for="prompt_title" class="wpb_element_label">
			<?php esc_html_e( 'Save element', 'js_composer' ); ?>
		</label>
		<?php
		// phpcs:ignore
		if ( is_string( $info ) ) { echo $info; }
		?>
	</div>
	<div class="vc_ui-prompt-content">
		<div class="vc_ui-prompt-column">
			<div class="wpb_el_type_textfield vc_wrapper-param-type-textfield vc_properties-list">
				<div class="edit_form_line">
					<?php
					WPB_Form_Field_Textfield::render(
						[
							'name' => 'title',
							'id'   => 'prompt_title',
							'classes' => 'wpb_vc_param_value h4 textfield',
						]
					);
					?>
				</div>
			</div>
		</div>
		<div class="vc_ui-prompt-column">
			<button type="submit"
				class="vc_general vc_ui-button vc_ui-button-size-md vc_ui-button-action vc_ui-button-shape-rounded vc_preset-save-btn" id="vc_ui-save-preset-btn"><?php esc_html_e( 'Save', 'js_composer' ); ?></button>
		</div>
	</div>
</form>
