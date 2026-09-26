<?php
/**
 * Configuration file for [vc_grid_container] shortcode of 'Grid Container' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Grid Container Title', 'js_composer' ),
		'param_name' => 'grid_container_title',
		'description' => esc_html__( 'This title is visible only in the admin area and helps site editors differentiate rows.', 'js_composer' ),
	],
	[
		'type' => 'range',
		'heading' => esc_html__( 'Columns', 'js_composer' ),
		'param_name' => 'columns',
		'value' => '2',
		'settings' => [
			'min' => '1',
			'max' => '12',
			'placeholder' => esc_html__( '3', 'js_composer' ),
		],
		'description' => esc_html__( 'Enter the number of columns for the grid.', 'js_composer' ),
	],
	[
		'type' => 'range',
		'heading' => esc_html__( 'Rows', 'js_composer' ),
		'param_name' => 'rows',
		'value' => '1',
		'settings' => [
			'min' => '1',
			'max' => '12',
			'placeholder' => esc_html__( '3', 'js_composer' ),
		],
		'description' => esc_html__( 'Enter the number of rows for the grid.', 'js_composer' ),
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Row gap', 'js_composer' ),
		'param_name' => 'row_gap',
		'value' => '',
		'placeholder' => '5px',
		'description' => esc_html__( 'Select gap between grid rows.', 'js_composer' ),
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Columns gap', 'js_composer' ),
		'param_name' => 'col_gap',
		'value' => '',
		'placeholder' => '5px',
		'description' => esc_html__( 'Select gap between grid columns.', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Grid container', 'js_composer' ),
	'is_container' => true,
	'icon' => 'icon-wpb-grid-container',
	'show_settings_on_create' => false,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'class' => 'vc_main-sortable-element',
	'description' => esc_html__( 'Build layout using CSS grid containers', 'js_composer' ),
	'as_child' => [ 'except' => 'vc_column,vc_grid_container_item,vc_row_inner,vc_column_inner,vc_flexbox_container_item' ],
	'as_parent' => [ 'only' => 'vc_grid_container_item' ],
	'js_view' => 'VcGridContainerView',
	'params' => array_merge( $params, vc_config()->get_css_animation_config( false ), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab() ),
];
