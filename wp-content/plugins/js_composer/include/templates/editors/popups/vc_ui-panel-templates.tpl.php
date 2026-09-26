<?php
/**
 * Templates panel template.
 *
 * @var Vc_Templates_Panel_Editor $box
 * @var string $id
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

vc_include_template( 'editors/popups/partials/modal-wrapper-start.php', [
	'id' => $id,
] );
?>
<!-- param window header-->
<?php
$categories_data = $box->getAllTemplatesSorted();
$categories = $box->getAllCategoriesNames( $categories_data );
?>
<?php
vc_include_template( 'editors/popups/vc_ui-header.tpl.php', [
	'id' => $id,
	'title' => esc_html__( 'Templates', 'js_composer' ),
	'controls' => [
		'panel-minimize' => [
			'title' => esc_html__( 'Minimize', 'js_composer' ),
		],
		'close' => [
			'title' => esc_html__( 'Close', 'js_composer' ),
		],
	],
	'header_css_class' => 'vc_ui-template-panel-header-container',
	'header_tabs_template' => 'editors/partials/vc_ui-templates-tabs.tpl.php',
	'search_template' => 'editors/partials/templates_search.tpl.php',
	'header_tabs_template_variables' => [
		'categories_data' => $categories_data,
		'categories' => $categories,
	],
	'box' => $box,
] );
?>
<!-- param window footer-->
<div class="vc_ui-panel-content-container">
	<div class="vc_ui-panel-content vc_properties-list"
		data-vc-ui-element="panel-content">
		<div class="vc_panel-tabs">
			<?php
			echo '<div class="vc-panel-no-templates-message">' . esc_html__( 'No templates found', 'js_composer' ) . '</div>';
			/**
			 * Preparing tabs content
			 */
			$first = true;
			foreach ( $categories_data as $key => $category ) :
				echo '<div class="vc_edit-form-tab vc_row vc_ui-flex-row' . ( $first ? ' vc_active' : '' ) . '" data-vc-ui-element="panel-edit-element-tab" data-tab="' . esc_attr( $category['category'] ) . '">';
				$templates_block = apply_filters( 'vc_templates_render_category', $category );
				if ( isset( $templates_block['output'] ) && is_string( $templates_block['output'] ) ) {
                    // @codingStandardsIgnoreLine
                    print $templates_block['output'];
				}
				echo '</div>';
				$first = false;
			endforeach;
			?>
		</div>
	</div>
</div>
<?php
vc_include_template( 'editors/popups/partials/modal-wrapper-end.php' );
?>
<!--/ temp content -->
