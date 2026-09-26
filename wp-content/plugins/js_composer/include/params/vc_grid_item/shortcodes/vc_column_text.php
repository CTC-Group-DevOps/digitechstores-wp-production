<?php
/**
 * Shortcode vc_column_text integration for vc_grid_item.
 *
 * @since 9.0
 * @var array $list
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$shortcode_vc_column_text = WPBMap::getShortCode( 'vc_column_text' );
if ( is_array( $shortcode_vc_column_text ) && isset( $shortcode_vc_column_text['base'] ) ) {
	$list['vc_column_text'] = $shortcode_vc_column_text;
	$list['vc_column_text']['post_type'] = Vc_Grid_Item_Editor::postType();
	$remove = [ 'el_id' ];
	foreach ( $list['vc_column_text']['params'] as $k => $v ) {
		if ( in_array( $v['param_name'], $remove, true ) ) {
			unset( $list['vc_column_text']['params'][ $k ] );
		}
	}
}
