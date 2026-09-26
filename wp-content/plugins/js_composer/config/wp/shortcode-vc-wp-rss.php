<?php
/**
 * Configuration file for [vc_wp_rss] shortcode of 'WP RSS' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' ),
		'section' => 'general',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'RSS feed URL', 'js_composer' ),
		'param_name' => 'url',
		'admin_label' => true,
		'section' => 'general',
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Items', 'js_composer' ),
		'param_name' => 'items',
		'value' => 10,
		'settings' => [
			'min' => 1,
			'max' => 20,
		],
		'admin_label' => true,
		'section' => 'general',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Display item content', 'js_composer' ),
		'param_name' => 'item_content',
		'section' => 'options',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Display item author (if available)', 'js_composer' ),
		'param_name' => 'item_author',
		'section' => 'options',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Display item date', 'js_composer' ),
		'param_name' => 'item_date',
		'section' => 'options',
	],
];

return [
	'name' => 'WP ' . esc_html__( 'RSS' ),
	'base' => 'vc_wp_rss',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'Entries from any RSS or Atom feed', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
	'sections' => array_merge( [ 'general', 'options' ], vc_config()->get_advanced_sections() ),
];
