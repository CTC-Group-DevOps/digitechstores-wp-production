<?php
/**
 * Shortcode vc_gitem_animated_block integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$list['vc_gitem_animated_block'] = [
	'base' => 'vc_gitem_animated_block',
	'name' => esc_html__( 'A/B block', 'js_composer' ),
	'content_element' => false,
	'is_container' => true,
	'show_settings_on_create' => false,
	'icon' => 'icon-wpb-gitem-block',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'controls' => [],
	'as_parent' => [
		'only' => [
			'vc_gitem_zone_a',
			'vc_gitem_zone_b',
		],
	],
	'params' => [
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Animation', 'js_composer' ),
			'param_name' => 'animation',
			'value' => WPBakeryShortCode_Vc_Gitem_Animated_Block::animations(),
		],
	],
	'js_view' => 'VcGitemAnimatedBlockView',
	'post_type' => Vc_Grid_Item_Editor::postType(),
];
