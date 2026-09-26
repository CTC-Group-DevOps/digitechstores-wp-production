<?php
/**
 * Shortcode vc_btn integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $vc_gitem_add_link_param
 * @var array $vc_gitem_add_link_target_param
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_vc_btn = WPBMap::getShortCode( 'vc_btn' );
if ( is_array( $shortcode_vc_btn ) && isset( $shortcode_vc_btn['base'] ) ) {
	$list['vc_btn'] = $shortcode_vc_btn;
	$list['vc_btn']['post_type'] = Vc_Grid_Item_Editor::postType();
	unset( $list['vc_btn']['params'][1] );
	$remove = [ 'el_id' ];
	foreach ( $list['vc_btn']['params'] as $k => $v ) {
		if ( in_array( $v['param_name'], $remove, true ) ) {
			unset( $list['vc_btn']['params'][ $k ] );
		}
	}
	array_unshift( $list['vc_btn']['params'], [
		'type' => 'vc_link',
		'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
		'param_name' => 'url',
		'dependency' => [
			'element' => 'link',
			'value' => [ 'custom' ],
		],
		'description' => esc_html__( 'Add custom link.', 'js_composer' ),
	] );
	array_unshift( $list['vc_btn']['params'], $vc_gitem_add_link_target_param );
	array_unshift( $list['vc_btn']['params'], $vc_gitem_add_link_param );
}
