<?php
/**
 * Edit element panel template.
 *
 * @var array $controls
 * @var Vc_Shortcode_Edit_Form $box
 * @var string $id
 * @var string $inner_classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
vc_include_template( 'editors/popups/partials/modal-wrapper-start.php', [
	'id' => $id,
	'inner_classes' => $inner_classes,
] );

vc_include_template( 'editors/popups/vc_ui-header.tpl.php', [
	'id' => $id,
	'title' => esc_html__( 'Edit element', 'js_composer' ),
	'controls' => $controls,
	'header_css_class' => 'vc_ui-post-settings-header-container',
	'content_template' => '',
	'box' => $box,
] );
?>

<!-- param window footer-->
<div class="vc_ui-panel-content-container">
	<div class="vc_ui-panel-content vc_properties-list vc_edit_form_elements">

		<!--/ temp content -->
	</div>
</div>
<!-- param window footer-->

<?php
do_action( 'wpb_add_element_controls' );

vc_include_template( 'editors/popups/partials/modal-wrapper-end.php' );
