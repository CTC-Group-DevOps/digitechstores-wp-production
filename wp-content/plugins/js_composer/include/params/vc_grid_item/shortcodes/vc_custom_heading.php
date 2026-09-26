<?php
/**
 * Shortcode vc_custom_heading integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 * @var array $vc_gitem_add_link_param
 * @var array $vc_gitem_add_link_target_param
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_vc_custom_heading = WPBMap::getShortCode( 'vc_custom_heading' );
if ( is_array( $shortcode_vc_custom_heading ) && isset( $shortcode_vc_custom_heading['base'] ) ) {
	$list['vc_custom_heading'] = $shortcode_vc_custom_heading;
	$list['vc_custom_heading']['post_type'] = Vc_Grid_Item_Editor::postType();
	$remove = [
		'link',
		'source',
		'el_id',
	];
	foreach ( $list['vc_custom_heading']['params'] as $k => $v ) {
		if ( in_array( $v['param_name'], $remove, true ) ) {
			unset( $list['vc_custom_heading']['params'][ $k ] );
		}

		// text depends on source. remove dependency so text is always saved.
		if ( 'text' === $v['param_name'] ) {
			unset( $list['vc_custom_heading']['params'][ $k ]['dependency'] );
		}
	}
	array_unshift( $list['vc_custom_heading']['params'], [
		'type' => 'vc_link',
		'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
		'param_name' => 'url',
		'dependency' => [
			'element' => 'link',
			'value' => [ 'custom' ],
		],
		'description' => esc_html__( 'Add custom link.', 'js_composer' ),
	] );
	array_unshift( $list['vc_custom_heading']['params'], $vc_gitem_add_link_target_param );
	array_unshift( $list['vc_custom_heading']['params'], $vc_gitem_add_link_param );
}
