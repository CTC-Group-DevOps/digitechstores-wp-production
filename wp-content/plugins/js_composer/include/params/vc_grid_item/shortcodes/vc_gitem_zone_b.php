<?php
/**
 * Shortcode vc_gitem_zone_b integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $zone_params
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

unset( $zone_params[0]['edit_field_class'] );

$list['vc_gitem_zone_b'] = [
	'name' => esc_html__( 'Hover', 'js_composer' ),
	'base' => 'vc_gitem_zone_b',
	'content_element' => false,
	'is_container' => true,
	'show_settings_on_create' => false,
	'icon' => 'icon-wpb-gitem-zone',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'controls' => [ 'edit' ],
	'as_parent' => [ 'only' => 'vc_gitem_row' ],
	'js_view' => 'VcGitemZoneView',
	'params' => $zone_params,
	'post_type' => Vc_Grid_Item_Editor::postType(),
	'sections' => vc_config()->get_advanced_sections(),
];
