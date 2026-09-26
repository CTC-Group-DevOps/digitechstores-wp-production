<?php
/**
 * Configuration file for [vc_tta_pageable] shortcode of 'Pageable Container' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'number',
		'param_name' => 'autoplay',
		'save_always' => true,
		'settings' => [
			'min' => 0,
			'step' => 1,
		],
		'value' => '7',
		'heading' => esc_html__( 'Autoplay', 'js_composer' ),
		'description' => esc_html__( 'Select auto rotate for pageable in seconds.', 'js_composer' ),
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
		'heading' => esc_html__( 'Pagination style', 'js_composer' ),
		'description' => esc_html__( 'Select pagination style.', 'js_composer' ),
		'std' => 'outline-round',
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'pagination',
	],
	[
		'type' => 'dropdown',
		'param_name' => 'tab_position',
		'value' => [
			esc_html__( 'Top', 'js_composer' ) => 'top',
			esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
		],
		'std' => 'bottom',
		'heading' => esc_html__( 'Position', 'js_composer' ),
		'description' => esc_html__( 'Select pageable navigation position.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'pagination',
	],
	[
		'type' => 'colorpicker',
		'param_name' => 'pagination_color',
		'settings' => [
			'default_colorpicker_color' => '#ebebeb',
		],
		'heading' => esc_html__( 'Pagination color', 'js_composer' ),
		'description' => esc_html__( 'Select pagination color.', 'js_composer' ),
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
		'section' => 'title',
	],
];

return [
	'name' => esc_html__( 'Pageable container', 'js_composer' ),
	'base' => 'vc_tta_pageable',
	'icon' => 'icon-wpb-ui-pageable',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_parent' => [
		'only' => 'vc_tta_section',
	],
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Pageable content container', 'js_composer' ),
	'js_view' => 'VcBackendTtaPageableView',
	'custom_markup' => '
<div class="vc_tta-container vc_tta-o-non-responsive" data-vc-action="collapse">
	<div class="vc_general vc_tta vc_tta-tabs vc_tta-pageable vc_tta-color-backend-tabs-white vc_tta-style-flat vc_tta-shape-rounded vc_tta-spacing-1 vc_tta-tabs-position-top vc_tta-controls-align-left">
		<div class="vc_tta-tabs-container"><ul class="vc_tta-tabs-list"><li class="vc_tta-tab" data-vc-tab data-vc-target-model-id="{{ model_id }}" data-element_type="vc_tta_section"><a href="javascript:;" data-vc-tabs data-vc-container=".vc_tta" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-target-model-id="{{ model_id }}"><span class="vc_tta-title-text">{{ section_title }}</span></a></li>' . '</ul>
		</div>
		<div class="vc_tta-panels vc_clearfix {{container-class}}">
		  {{ content }}
		</div>
	</div>
</div>',
	'default_content' => '
[vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Section', 'js_composer' ), 1 ) . '"][/vc_tta_section]
[vc_tta_section title="' . sprintf( '%s %d', esc_html__( 'Section', 'js_composer' ), 2 ) . '"][/vc_tta_section]
	',
	'admin_enqueue_js' => [
		vc_asset_url( 'lib/vc/vc_tabs/vc-tabs.min.js' ),
	],
	'sections' => array_merge( [ 'autoplay', 'pagination', 'title' ], vc_config()->get_advanced_sections() ),
	'params' => vc_config()->merge_default_params( $params ),
];
