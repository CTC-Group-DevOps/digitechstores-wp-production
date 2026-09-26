<?php
/**
 * Shortcode vc_gitem integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem'] = [
	'name' => esc_html__( 'Grid item', 'js_composer' ),
	'base' => 'vc_gitem',
	'is_container' => true,
	'icon' => 'icon-wpb-gitem',
	'content_element' => false,
	'show_settings_on_create' => false,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Main grid item', 'js_composer' ),
	'params' => [
		[
			'type' => 'css_editor',
			'param_name' => 'css',
			'section' => vc_config()->get_design_options_css_box_section_slug(),
		],
		vc_config()->get_extra_class_params( false ),
	],
	'sections' => vc_config()->get_advanced_sections(),
	'js_view' => 'VcGitemView',
	'post_type' => Vc_Grid_Item_Editor::postType(),
];
