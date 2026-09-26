<?php
/**
 * Shortcode vc_icon integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $vc_gitem_add_link_param
 * @var array $vc_gitem_add_link_target_param
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_vc_icon = WPBMap::getShortCode( 'vc_icon' );
if ( is_array( $shortcode_vc_icon ) && isset( $shortcode_vc_icon['base'] ) ) {
	$list['vc_icon'] = $shortcode_vc_icon;
	$list['vc_icon']['post_type'] = Vc_Grid_Item_Editor::postType();
	$list['vc_icon']['params'] = vc_map_integrate_shortcode( 'vc_icon', '', '', [
		'exclude' => [
			'link',
			'el_id',
		],
	], false, true );
	array_unshift( $list['vc_icon']['params'], [
		'type' => 'vc_link',
		'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
		'param_name' => 'url',
		'dependency' => [
			'element' => 'link',
			'value' => [ 'custom' ],
		],
		'description' => esc_html__( 'Add custom link.', 'js_composer' ),
	] );
	array_unshift( $list['vc_icon']['params'], $vc_gitem_add_link_target_param );
	array_unshift( $list['vc_icon']['params'], $vc_gitem_add_link_param );
}
