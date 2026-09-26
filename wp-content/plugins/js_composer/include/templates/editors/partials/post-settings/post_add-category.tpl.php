<?php
/**
 *  Add new category section in post settings panel template.
 *
 * @since 8.2
 *
 * @var object $vc_settings_category_manager
 */

?>
<div id="vc_add-new-category" class="vc_ui-hidden">
	<label for="vc_new-category" class="wpb_element_label"><?php esc_html_e( 'Category name', 'js_composer' ); ?></label>
	<input id="vc_new-category" class="wpb-form-input" type="text" />
	<label for="vc_new-category-parent" class="wpb_element_label"><?php esc_html_e( 'Parent category', 'js_composer' ); ?></label>
	<?php
	WPB_Form_Field_Dropdown::render( [
		'id' => 'vc_new-category-parent',
		'options' => $vc_settings_category_manager->get_category_options_with_indent( null, 0, false ),
	] );
	?>
</div>
<div class="vc_add-new-category-controls">
	<button id="vc_add-new-category-close" class="vc_general vc_ui-button vc_ui-button-default vc_ui-button-shape-rounded vc_ui-hidden" type="button"><?php esc_html_e( 'Close', 'js_composer' ); ?></button>
	<button id="vc_add-new-category-open" class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded" type="button" data-action="toggle"><?php esc_html_e( 'Add new category', 'js_composer' ); ?></button>
</div>
