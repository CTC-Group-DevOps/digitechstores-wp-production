<?php
/**
 * Class that handles specific [vc_video] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_video.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Video
 */
class WPBakeryShortCode_Vc_Video extends WPBakeryShortCode {
	/**
	 * Get CSS file names for vc_video shortcode.
	 *
	 * @since 9.0
	 * @return array
	 */
	public function get_shortcode_css_files() {
		return [ 'vc_video_widget' ];
	}
}
