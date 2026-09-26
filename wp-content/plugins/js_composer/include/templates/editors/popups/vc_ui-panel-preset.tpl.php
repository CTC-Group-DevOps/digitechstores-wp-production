<?php
/**
 * Preset panel template.
 *
 * @var Vc_Preset_Panel_Editor $box
 * @var string $id
 * @var string $wrapper_classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
vc_include_template( 'editors/popups/partials/modal-wrapper-start.php', [
	'id' => $id,
	'wrapper_classes' => $wrapper_classes,
] );

vc_include_template( 'editors/popups/vc_ui-header.tpl.php', [
	'id' => $id,
	'title' => esc_html__( 'My Elements', 'js_composer' ),
	'controls' => [
		'panel-minimize' => [
			'title' => esc_html__( 'Minimize', 'js_composer' ),
		],
		'close' => [
			'title' => esc_html__( 'Close', 'js_composer' ),
		],
	],
	'header_css_class' => 'vc_ui-preset-panel-header-container',
] );
?>
<!-- param window footer-->
<div class="vc_ui-panel-content-container">
	<div class="vc_ui-panel-content vc_properties-list vc_row"
			data-vc-ui-element="panel-content">
		<div class="vc_column vc_col-sm-12">
			<h3><?php esc_html_e( 'Manage My Elements', 'js_composer' ); ?></h3>
			<p class="vc_description"><?php esc_html_e( 'Remove existing elements', 'js_composer' ); ?></p>
		</div>
		<div class="vc_column vc_col-sm-12">
			<div class="vc_ui-template-list vc_ui-list-bar" data-vc-action="collapseAll" data-vc-presets-list-content>
				<div class="vc_ui-template" style="display:none;">
					<div class="vc_ui-list-bar-item">
						<button type="button" class="vc_ui-list-bar-item-trigger" title="" data-vc-ui-element="template-title"></button>
						<div class="vc_ui-list-bar-item-actions">
							<button type="button" class="vc_general vc_ui-control-button" title="<?php esc_attr_e( 'Add element', 'js_composer' ); ?>" data-template-handler data-vc-ui-add-preset><i class="vc-composer-icon vc-c-icon-add"></i></button>
							<button type="button" class="vc_general vc_ui-control-button" data-vc-ui-delete="preset-title" data-preset="" data-preset-parent="" title="<?php esc_attr_e( 'Delete element', 'js_composer' ); ?>">
								<i class="vc-composer-icon vc-c-trash"></i></button>
						</div>
					</div>
				</div>
				<?php
                // @codingStandardsIgnoreLine
                print $box->getPresets();
				?>
			</div>
		</div>
	</div>
</div>
<?php
vc_include_template( 'editors/popups/partials/modal-wrapper-end.php' );
?>
<!--/ temp content -->
