<?php
/**
 * Configuration file for [vc_widget_sidebar] shortcode of 'Widgetised Sidebar' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		'admin_label' => true,
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'widgetised_sidebars',
		'heading' => esc_html__( 'Sidebar', 'js_composer' ),
		'param_name' => 'sidebar_id',
		'description' => esc_html__( 'Select widget area to display.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
];

return [
	'name' => esc_html__( 'Widgetised sidebar', 'js_composer' ),
	'base' => 'vc_widget_sidebar',
	'class' => 'wpb_widget_sidebar_widget',
	'icon' => 'icon-wpb-layout_sidebar',
	'category' => esc_html__( 'Structure', 'js_composer' ),
	'description' => esc_html__( 'WordPress widgetised sidebar', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
];
