<?php
/**
 * Configuration file for [vc_tta_section] shortcode of 'Section' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$parent_tag = vc_post_param( 'parent_tag', '' );
$include_icon_params = ( 'vc_tta_pageable' !== $parent_tag );

// The single section element is shared by Tabs, Tour, Accordion and Pageable, so its name adapts to the parent element.
$section_titles_by_parent = [
	'vc_tta_tabs' => esc_html__( 'Tab section', 'js_composer' ),
	'vc_tta_tour' => esc_html__( 'Tour section', 'js_composer' ),
	'vc_tta_accordion' => esc_html__( 'Accordion section', 'js_composer' ),
	'vc_tta_pageable' => esc_html__( 'Pageable container section', 'js_composer' ),
];
$section_name = isset( $section_titles_by_parent[ $parent_tag ] ) ? $section_titles_by_parent[ $parent_tag ] : esc_html__( 'Section', 'js_composer' );
$section_id_heading = sprintf( esc_html__( '%s id', 'js_composer' ), $section_name );

if ( $include_icon_params ) {
	require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-icon-element.php' );
	$add_icon_params = [
		[
			'type' => 'toggle',
			'param_name' => 'add_icon',
			'heading' => esc_html__( 'Add icon', 'js_composer' ),
			'description' => esc_html__( 'Add icon next to section title.', 'js_composer' ),
		],
	];
	$icon_tab_params = [
		[
			'type' => 'dropdown',
			'param_name' => 'i_position',
			'value' => [
				esc_html__( 'Before title', 'js_composer' ) => 'left',
				esc_html__( 'After title', 'js_composer' ) => 'right',
			],
			'dependency' => [
				'element' => 'add_icon',
				'value' => 'true',
			],
			'heading' => esc_html__( 'Icon position', 'js_composer' ),
			'description' => esc_html__( 'Select icon position.', 'js_composer' ),
			'group' => esc_html__( 'Icon', 'js_composer' ),
		],
	];
	$icon_tab_params = array_merge( $icon_tab_params, (array) vc_map_integrate_shortcode( vc_icon_element_params(), 'i_', esc_html__( 'Icon', 'js_composer' ), [
		// we need only type, icon_fontawesome, icon_.., NOT color and etc.
		'include_only_regex' => '/^(type|icon_\w*)/',
	], [
		'element' => 'add_icon',
		'value' => 'true',
	] ) );
} else {
	$add_icon_params = [];
	$icon_tab_params = [];
}

$params = array_merge(
	[
		[
			'type' => 'textfield',
			'param_name' => 'title',
			'heading' => esc_html__( 'Title', 'js_composer' ),
			'description' => esc_html__( 'Enter section title (Note: you can leave it empty).', 'js_composer' ),
		],
		[
			'type' => 'el_id',
			'param_name' => 'tab_id',
			'settings' => [
				'auto_generate' => true,
			],
			'heading' => $section_id_heading,
			'description' => sprintf( esc_html__( 'Enter section ID (Note: make sure it is unique and valid according to %1$sw3c specification%2$s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
		],
	],
	$add_icon_params,
	$icon_tab_params,
	[ vc_config()->get_extra_class_params( false ) ],
);

return [
	'name' => $section_name,
	'title_by_parent' => $section_titles_by_parent,
	'base' => 'vc_tta_section',
	'icon' => 'icon-wpb-ui-tta-section',
	'allowed_container_element' => 'vc_row',
	'is_container' => true,
	'show_settings_on_create' => false,
	'as_child' => [
		'only' => 'vc_tta_tour,vc_tta_tabs,vc_tta_accordion,vc_tta_pageable',
	],
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Section for Tabs, Tours, Accordions.', 'js_composer' ),
	'params' => $params,
	'js_view' => 'VcBackendTtaSectionView',
	'custom_markup' => '
		<div class="vc_tta-panel-heading">
		    <h4 class="vc_tta-panel-title vc_tta-controls-icon-position-left"><a href="javascript:;" data-vc-target="[data-model-id=\'{{ model_id }}\']" data-vc-accordion data-vc-container=".vc_tta-container"><span class="vc_tta-title-text">{{ section_title }}</span><i class="vc_tta-controls-icon vc_tta-controls-icon-plus"></i></a></h4>
		</div>
		<div class="vc_tta-panel-body">
			{{ editor_controls }}
			<div class="{{ container-class }}">
			{{ content }}
			</div>
		</div>',
	'default_content' => '',
];
