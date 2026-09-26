<?php
/**
 * Class that handles specific [vc_icon] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_icon.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Icon
 *
 * @since 4.4
 */
class WPBakeryShortCode_Vc_Icon extends WPBakeryShortCode {
	/**
	 * Get CSS file names for vc_icon shortcode.
	 *
	 * @since 9.0
	 * @return string
	 */
	public function get_shortcode_css_files() {
		return 'vc_icon_element';
	}
}
