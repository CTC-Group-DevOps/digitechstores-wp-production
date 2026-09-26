<?php
/**
 * Save Current Layout Template
 *
 * Reuses the `.vc_ui-prompt-content` flex helpers (defined for the element preset prompt)
 * so the input + button align at matching heights without bespoke CSS for this form.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="vc_column vc_col-sm-12 vc_ui-save-template-form" data-vc-hide-on-search="true">
	<label for="vc_ui-save-template" class="vc_element_label">
		<?php echo esc_html__( 'Save layout as a template', 'js_composer' ); ?>
	</label>
	<div class="vc_ui-prompt-content">
		<div class="vc_ui-prompt-column">
			<?php
			WPB_Form_Field_Textfield::render(
				[
					'id' => 'vc_ui-save-template',
					'name' => 'padding',
					'classes' => 'vc_panel-templates-name',
					'placeholder' => esc_attr__( 'Enter name of your template', 'js_composer' ),
					'data_attr_list' => [ 'js-element' => 'vc-templates-input' ],
				]
			);
			?>
		</div>
		<div class="vc_ui-prompt-column">
			<button class="vc_general vc_ui-button vc_ui-button-size-md vc_ui-button-action vc_ui-button-shape-rounded vc_template-save-btn" id="vc_ui-save-template-btn" data-vc-ui-element="button-save">
				<?php echo esc_html__( 'Save template', 'js_composer' ); ?>
			</button>
		</div>
	</div>
</div>
