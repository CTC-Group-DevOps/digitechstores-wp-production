<?php
/**
 * Configuration file for [wpb_goo_maps] shortcode of 'Google Maps' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Location', 'js_composer' ),
		'param_name' => 'location',
		'value' => 'London Eye, London, United Kingdom',
		'admin_label' => true,
		'description' => esc_html__( 'Enter a location for the map. You can specify an address, city, country, or coordinates.', 'js_composer' ),
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Height', 'js_composer' ),
		'param_name' => 'height',
		'value' => '',
		'admin_label' => true,
		'description' => esc_html__( 'Enter map height (in pixels or leave empty for responsive map).', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'settings' => [
			'units' => [ 'px' ],
			'min' => 0,
		],
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Zoom', 'js_composer' ),
		'param_name' => 'zoom',
		'value' => '10',
		'admin_label' => true,
		'description' => esc_html__( 'Adjust the map zoom level (0–22). You can set it to a specific value to control how close or far the view appears.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'settings' => [
			'min' => 0,
			'max' => 22,
		],
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Map type', 'js_composer' ),
		'param_name' => 'type',
		'value' => [
			esc_html__( 'Default', 'js_composer' ) => 'm',
			esc_html__( 'Satellite', 'js_composer' ) => 'k',
			esc_html__( 'Hybrid', 'js_composer' ) => 'h',
		],
		'admin_label' => true,
		'description' => esc_html__( 'Select the type of map to display.', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Google maps', 'js_composer' ),
	'base' => 'vc_goo_maps',
	'icon' => 'icon-wpb-map-pin',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Map block', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
];
