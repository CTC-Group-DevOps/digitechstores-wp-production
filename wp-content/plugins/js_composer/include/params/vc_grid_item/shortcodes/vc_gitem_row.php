<?php
/**
 * Shortcode vc_gitem_row integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_row'] = [
	'name' => esc_html__( 'Row', 'js_composer' ),
	'base' => 'vc_gitem_row',
	'content_element' => false,
	'is_container' => true,
	'icon' => 'icon-wpb-row',
	'weight' => 1000,
	'show_settings_on_create' => false,
	'controls' => [
		'layout',
		'delete',
	],
	'allowed_container_element' => 'vc_gitem_col',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Place content elements inside the row', 'js_composer' ),
	'params' => [
		vc_config()->get_extra_class_params(),
	],
	'js_view' => 'VcGitemRowView',
	'post_type' => Vc_Grid_Item_Editor::postType(),
	'sections' => vc_config()->get_advanced_sections(),
];
