<?php
/**
 * UI Panel Post Settings template.
 *
 * @var array $page_settings_data
 * @var Vc_Post_Settings $box
 * @var array $header_tabs_template_variables
 * @var array $controls
 * @var array $permalink
 * @var string $id
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

vc_include_template( 'editors/popups/partials/modal-wrapper-start.php', [
	'id' => $id,
] );

vc_include_template(
	'editors/popups/vc_ui-header.tpl.php',
	[
		'id' => $id,
		'title' => sprintf( __( '%s settings', 'js_composer' ), wpb_get_post_type_noun() ),
		'controls' => [
			'panel-minimize' => [
				'title' => esc_html__( 'Minimize', 'js_composer' ),
			],
			'close' => [
				'title' => esc_html__( 'Close', 'js_composer' ),
			],
		],
		'header_css_class' => 'vc_ui-post-settings-header-container',
		'box' => $box,
	]
);
?>
<div class="vc_ui-panel-content-container">
	<div class="vc_ui-panel-content vc_properties-list vc_edit_form_elements" data-vc-ui-element="panel-content">
		<form id="vc_settings-post-settings-form" action method="post" class="vc_edit-form-tab">
			<?php
			vc_include_template(
				'editors/popups/page-settings/page-settings-tab.tpl.php',
				[
					'page_settings_data' => $page_settings_data,
					'permalink' => $permalink,
				]
			);
			?>
		</form>
	</div>
</div>
<?php
// Include the template with the dynamic controls array.
vc_include_template(
	'editors/popups/vc_ui-footer.tpl.php',
	[
		'controls' => $controls,
	]
);

vc_include_template( 'editors/popups/partials/modal-wrapper-end.php' );
