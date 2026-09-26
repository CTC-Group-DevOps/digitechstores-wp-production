<?php
/**
 * Shortcode vc_separator integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_vc_separator = WPBMap::getShortCode( 'vc_separator' );
if ( is_array( $shortcode_vc_separator ) && isset( $shortcode_vc_separator['base'] ) ) {
	$list['vc_separator'] = $shortcode_vc_separator;
	$list['vc_separator']['post_type'] = Vc_Grid_Item_Editor::postType();
	$remove = [ 'el_id' ];
	foreach ( $list['vc_separator']['params'] as $k => $v ) {
		if ( in_array( $v['param_name'], $remove, true ) ) {
			unset( $list['vc_separator']['params'][ $k ] );
		}
	}
}
