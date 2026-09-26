<?php
/**
 * Configuration file for [vc_flexbox_container] shortcode of 'Flex Container' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Flexbox Container Title', 'js_composer' ),
		'param_name' => 'flexbox_container_title',
		'description' => esc_html__( 'This title is visible only in the admin area and helps site editors differentiate rows.', 'js_composer' ),
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Gap', 'js_composer' ),
		'param_name' => 'gap',
		'value' => '0px',
		'settings' => [
			'placeholder' => 'e.g. 5px',
		],
		'description' => esc_html__( 'Select gap between flex items.', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Flexbox container', 'js_composer' ),
	'is_container' => true,
	'icon' => 'icon-wpb-flexbox-container',
	'show_settings_on_create' => false,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'class' => 'vc_main-sortable-element',
	'description' => esc_html__( 'Build layout using flexbox containers', 'js_composer' ),
	'as_child' => [ 'except' => 'vc_column,vc_flexbox_container_item,vc_row_inner,vc_column_inner,vc_grid_container_item' ],
	'as_parent' => [ 'only' => 'vc_flexbox_container_item' ],
	'params' => array_merge( $params, vc_config()->get_css_animation_config( false ), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab() ),
	'js_view' => 'VcFlexboxContainerView',
];
