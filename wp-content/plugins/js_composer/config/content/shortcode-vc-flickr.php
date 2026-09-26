<?php
/**
 * Configuration file for [vc_flickr] shortcode of 'Flickr Widget' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Flickr ID', 'js_composer' ),
		'param_name' => 'flickr_id',
		'value' => '95572727@N00',
		'admin_label' => true,
		'description' => sprintf( esc_html__( 'To find your flickID visit %s.', 'js_composer' ), '<a href="https://www.webfx.com/tools/idgettr/" target="_blank">idGettr</a>' ),
		'edit_field_class' => 'vc_col-xs-6',
		'wpb_param_section_start' => true,
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Type', 'js_composer' ),
		'param_name' => 'type',
		'value' => [
			esc_html__( 'User', 'js_composer' ) => 'user',
			esc_html__( 'Group', 'js_composer' ) => 'group',
		],
		'description' => esc_html__( 'Select photo stream type.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Number of photos', 'js_composer' ),
		'param_name' => 'count',
		'value' => 9,
		'description' => esc_html__( 'Select number of photos to display.', 'js_composer' ),
		'settings' => [
			'min' => 1,
			'max' => 20,
		],
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Display order', 'js_composer' ),
		'param_name' => 'display',
		'value' => [
			esc_html__( 'Latest first', 'js_composer' ) => 'latest',
			esc_html__( 'Random', 'js_composer' ) => 'random',
		],
		'description' => esc_html__( 'Select photo display order.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'wpb_param_section_end' => true,
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		'wpb_param_section_start' => true,
		'wpb_param_section_end' => true,
	],
];

return [
	'base' => 'vc_flickr',
	'name' => esc_html__( 'Flickr widget', 'js_composer' ),
	'icon' => 'icon-wpb-flickr',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Image feed from Flickr account', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
];
