<?php
/**
 * UI panel row layout template.
 *
 * @var array $vc_row_layouts
 * @var string $row_info
 * @var Vc_Edit_Layout $box
 * @var string $id
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

vc_include_template( 'editors/popups/partials/modal-wrapper-start.php', [
	'id' => $id,
] );

vc_include_template('editors/popups/vc_ui-header.tpl.php', [
	'id' => $id,
	'title' => esc_html__( 'Row Layout', 'js_composer' ),
	'controls' => [
		'panel-minimize' => [
			'title' => esc_html__( 'Minimize', 'js_composer' ),
		],
		'close' => [
			'title' => esc_html__( 'Close', 'js_composer' ),
		],
	],
	'header_css_class' => 'vc_ui-row-layout-header-container',
]);
?>
<div class="vc_ui-panel-content-container">
	<div class="vc_ui-panel-content vc_properties-list vc_edit_form_elements" data-vc-ui-element="panel-content">
		<div class="vc_row vc_ui-flex-row">
			<div class="vc_col-sm-12 vc_column vc_layout-panel-switcher">
				<div class="wpb-param-heading">
					<div class="wpb_element_label"><?php esc_html_e( 'Row layout', 'js_composer' ); ?></div>
					<?php
					if ( is_string( $row_info ) ) {
                        // phpcs:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $row_info;
					}
					?>
				</div>
				<?php
				$layout_options = [];
				foreach ( $vc_row_layouts as $layout ) {
					$layout_options[ $layout['cells'] ] = [
						'label' => 'vc-c-icon-' . $layout['icon_class'],
						'title' => $layout['title'],
					];
				}
				WPB_Form_Field_Button_Group::render(
					[
						'id' => 'vc_row-layout-preset',
						'name' => 'vc_row_layout_preset',
						'class' => 'vc_row-layout-presets',
						'options' => $layout_options,
						'heading' => esc_html__( 'Row layout', 'js_composer' ),
						'current_value' => '11',
					]
				);
				?>

			</div>
			<div class="vc_col-sm-12 vc_column">
				<div class="edit_form_line">
					<?php
					WPB_Form_Field_Textfield::render(
						[
							'id'   => 'vc_row-layout',
							'name' => 'padding',
							'classes' => 'vc_row_layout',
						]
					);
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
vc_include_template( 'editors/popups/partials/modal-wrapper-end.php' );
