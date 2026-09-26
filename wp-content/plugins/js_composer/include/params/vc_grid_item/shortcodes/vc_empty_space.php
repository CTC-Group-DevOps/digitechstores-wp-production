<?php
/**
 * Shortcode vc_empty_space integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_vc_empty_space = WPBMap::getShortCode( 'vc_empty_space' );
if ( is_array( $shortcode_vc_empty_space ) && isset( $shortcode_vc_empty_space['base'] ) ) {
	$list['vc_empty_space'] = $shortcode_vc_empty_space;
	$list['vc_empty_space']['post_type'] = Vc_Grid_Item_Editor::postType();
	$remove = [ 'el_id' ];
	foreach ( $list['vc_empty_space']['params'] as $k => $v ) {
		if ( in_array( $v['param_name'], $remove, true ) ) {
			unset( $list['vc_empty_space']['params'][ $k ] );
		}
	}
}
