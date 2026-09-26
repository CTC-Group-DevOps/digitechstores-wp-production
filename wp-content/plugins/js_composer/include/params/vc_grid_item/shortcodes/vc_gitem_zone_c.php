<?php
/**
 * Shortcode vc_gitem_zone_c integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_zone_c'] = [
	'name' => esc_html__( 'Additional', 'js_composer' ),
	'base' => 'vc_gitem_zone_c',
	'content_element' => false,
	'is_container' => true,
	'show_settings_on_create' => false,
	'icon' => 'icon-wpb-gitem-zone',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'controls' => [
		'move',
		'delete',
		'edit',
	],
	'as_parent' => [ 'only' => 'vc_gitem_row' ],
	'js_view' => 'VcGitemZoneCView',
	'params' => [
		[
			'type' => 'css_editor',
			'param_name' => 'css',
		],
		vc_config()->get_extra_class_params(),
	],
	'post_type' => Vc_Grid_Item_Editor::postType(),
	'sections' => vc_config()->get_advanced_sections(),
];
