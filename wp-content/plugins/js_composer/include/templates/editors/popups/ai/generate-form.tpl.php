<?php
/**
 * Generate AI form template.
 * Structure is similar to Edit Form, to preserve styling.
 *
 * @var string $element_form_fields_template_path
 * @var string $ai_element_type
 * @var string $ai_element_id
 * @var Vc_Ai_Modal_Controller $ai_modal_controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="vc_ui-panel-content-container vc_ui-hidden">
	<div class="vc_ui-panel-content vc_properties-list wpb_edit_form_elements vc_edit_form_elements" data-vc-ui-element="panel-content">
		<form method="post" action="" class="vc_edit-form-tab vc_row vc_ui-flex-row">
			<?php
			vc_include_template(
				$element_form_fields_template_path,
				[
					'ai_element_type' => $ai_element_type,
					'ai_element_id' => $ai_element_id,
					'ai_modal_controller' => $ai_modal_controller,
				]
			);
			?>
		</form>
	</div>
</div>
