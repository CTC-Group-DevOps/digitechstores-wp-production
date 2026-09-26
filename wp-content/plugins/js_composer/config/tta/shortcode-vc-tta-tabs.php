<?php
/**
 * Configuration file for [vc_tta_tabs] shortcode of 'Tabs' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'dropdown',
		'param_name' => 'style',
		'value' => [
			esc_html__( 'Classic', 'js_composer' ) => 'classic',
			esc_html__( 'Modern', 'js_composer' ) => 'modern',
			esc_html__( 'Flat', 'js_composer' ) => 'flat',
			esc_html__( 'Outline', 'js_composer' ) => 'outline',
		],
		'heading' => esc_html__( 'Style', 'js_composer' ),
		'description' => esc_html__( 'Select tabs display style.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'dropdown',
		'param_name' => 'shape',
		'value' => [
			esc_html__( 'Rounded', 'js_composer' ) => 'rounded',
			esc_html__( 'Square', 'js_composer' ) => 'square',
			esc_html__( 'Round', 'js_composer' ) => 'round',
		],
		'heading' => esc_html__( 'Shape', 'js_composer' ),
		'description' => esc_html__( 'Select tabs shape.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'colorpicker',
		'param_name' => 'color',
		'heading' => esc_html__( 'Inactive color', 'js_composer' ),
		'description' => esc_html__( 'Select tabs color.', 'js_composer' ),
		'std' => '#EBEBEB',
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'colorpicker',
		'param_name' => 'active_color',
		'heading' => esc_html__( 'Active color', 'js_composer' ),
		'description' => esc_html__( 'Select active tab color.', 'js_composer' ),
		'std' => '#F8F8F8',
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'toggle',
		'param_name' => 'fill_content_area',
		'std' => 'true',
		'heading' => esc_html__( 'Fill content area', 'js_composer' ),
		'description' => esc_html__( 'Enable to fill the content area with the active color.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'styles',
	],
	[
		'type' => 'dropdown',
		'param_name' => 'section_title_tag',
		'value' => [
			'h1' => 'h1',
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6',
			'p' => 'p',
		],
		'std' => 'h4',
		'heading' => esc_html__( 'Title tag', 'js_composer' ),
		'description' => esc_html__( 'Select title tag.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'button_group',
		'param_name' => 'alignment',
		'heading' => esc_html__( 'Alignment', 'js_composer' ),
		'value'       => vc_config()->get_text_align_param_value(),
		'std' => 'left',
		'description' => esc_html__( 'Select tabs section title alignment.', 'js_composer' ),
		'section' => 'structure',
	],
	[
		'type' => 'dropdown',
		'param_name' => 'tab_position',
		'value' => [
			esc_html__( 'Top', 'js_composer' ) => 'top',
			esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
		],
		'heading' => esc_html__( 'Position', 'js_composer' ),
		'description' => esc_html__( 'Select tabs navigation position.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'number',
		'param_name' => 'spacing',
		'heading' => esc_html__( 'Spacing', 'js_composer' ),
		'description' => esc_html__( 'Select tabs spacing.', 'js_composer' ),
		'std' => '1',
		'settings' => [
			'min' => 0,
			'step' => 1,
			'units' => true,
		],
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'number',
		'param_name' => 'gap',
		'std' => '0',
		'settings' => [
			'min' => 0,
			'step' => 1,
			'units' => true,
		],
		'heading' => esc_html__( 'Gap', 'js_composer' ),
		'description' => esc_html__( 'Select tabs gap.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'structure',
	],
	[
		'type' => 'number',
		'param_name' => 'autoplay',
		'std' => '',
		'settings' => [
			'min' => 0,
			'step' => 1,
		],
		'heading' => esc_html__( 'Autoplay', 'js_composer' ),
		'description' => esc_html__( 'Select auto rotate for tabs in seconds (Note: disabled by default).', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'autoplay',
	],
	[
		'type' => 'number',
		'param_name' => 'active_section',
		'heading' => esc_html__( 'Active section', 'js_composer' ),
		'value' => '1',
		'settings' => [
			'min' => 0,
			'step' => 1,
		],
		'description' => esc_html__( 'Enter active section number (Note: to have all sections closed on initial load enter non-existing number).', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'autoplay',
	],
	[
		'type' => 'dropdown',
		'param_name' => 'pagination_style',
		'value' => [
			esc_html__( 'None', 'js_composer' ) => '',
			esc_html__( 'Square Dots', 'js_composer' ) => 'outline-square',
			esc_html__( 'Radio Dots', 'js_composer' ) => 'outline-round',
			esc_html__( 'Point Dots', 'js_composer' ) => 'flat-round',
			esc_html__( 'Fill Square Dots', 'js_composer' ) => 'flat-square',
			esc_html__( 'Rounded Fill Square Dots', 'js_composer' ) => 'flat-rounded',
		],
		'heading' => esc_html__( 'Pagination', 'js_composer' ),
		'description' => esc_html__( 'Select pagination style.', 'js_composer' ),
		'section' => 'pagination',
	],
	[
		'type' => 'colorpicker',
		'param_name' => 'pagination_color',
		'value' => '#ebebeb',
		'heading' => esc_html__( 'Pagination color', 'js_composer' ),
		'description' => esc_html__( 'Select pagination color.', 'js_composer' ),
		'param_holder_class' => 'vc_colored-dropdown',
		'std' => '#ebebeb',
		'dependency' => [
			'element' => 'pagination_style',
			'not_empty' => true,
		],
		'section' => 'pagination',
	],
	[
		'type' => 'textfield',
		'param_name' => 'title',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
	],
	[
		'type' => 'dropdown',
		'param_name' => 'title_tag',
		'value' => [
			'h1' => 'h1',
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6',
			'p' => 'p',
		],
		'std' => 'h2',
		'heading' => esc_html__( 'Widget title tag', 'js_composer' ),
		'description' => esc_html__( 'Select widget title tag.', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Tabs', 'js_composer' ),
	'base' => 'vc_tta_tabs',
	'icon' => 'icon-wpb-ui-tab-content',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_parent' => [
		'only' => 'vc_tta_section',
	],
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Tabbed content', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params ),
	'js_view' => 'VcBackendTtaTabsView',
	'sections' => array_merge( [ 'styles', 'structure', 'autoplay', 'pagination' ], vc_config()->get_advanced_sections() ),
	'custom_markup' => '
<div class="vc_tta-container" data-vc-action="collapse">
	<div class="vc_general vc_tta vc_tta-tabs vc_tta-color-backend-tabs-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-spacing-1 vc_tta-tabs-position-top vc_tta-controls-align-left">
		<div class="vc_tta-tabs-container"><ul class="vc_tta-tabs-list"><li class="vc_tta-tab" data-vc-tab data-vc-target-model-id="{{ model_id }}" data-element_type="vc_tta_section"><a href="javascript:;" data-vc-tabs data-vc-container=".vc_tta" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-target-model-id="{{ model_id }}"><span class="vc_tta-title-text">{{ section_title }}</span></a></li>' . '</ul>
		</div>
		<div class="vc_tta-panels vc_clearfix {{container-class}}">
		  {{ content }}
		</div>
	</div>
</div>',
	'default_content' => '
[vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Tab', 'js_composer' ), 1 ) . '"][/vc_tta_section]
[vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Tab', 'js_composer' ), 2 ) . '"][/vc_tta_section]
	',
	'admin_enqueue_js' => [
		vc_asset_url( 'lib/vc/vc_tabs/vc-tabs.min.js' ),
	],
];
