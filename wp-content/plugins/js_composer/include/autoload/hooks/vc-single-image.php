<?php
/**
 * Autoload hooks and ajax actions related to single image.
 *
 * @note we require our autoload files everytime and everywhere after plugin load.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'wp_ajax_wpb_single_image_src', 'vc_single_image_src' );

/**
 * Get Single Image source URL.
 */
function vc_single_image_src() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

	$image_id = (int) vc_post_param( 'content' );
	$params = vc_post_param( 'params' );
	$post_id = (int) vc_post_param( 'post_id' );
	$img_size = vc_post_param( 'size' );

	if ( ! empty( $params['source'] ) ) {
		$source = $params['source'];
	} else {
		$source = 'media_library';
	}

	$image_data = wpb_get_image_data_by_source( $source, $post_id, $image_id, $img_size, $params );

	echo esc_url( $image_data['image_src'] );
	die();
}
