<?php
/**
 * Configuration file for [vc_column_inner] shortcode of 'Inner Column' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return [
	'name' => __( 'Inner Column', 'js_composer' ),
	'base' => 'vc_column_inner',
	'icon' => 'icon-wpb-column',
	'class' => '',
	'wrapper_class' => '',
	'controls' => 'full',
	'allowed_container_element' => false,
	'content_element' => false,
	'is_container' => true,
	'description' => esc_html__( 'Place content elements inside the inner column', 'js_composer' ),
	'js_view' => 'VcColumnView',
	'params' => array_merge(
		vc_config()->get_general_advanced_settings(),
		vc_config()->get_design_options_tab(),
		vc_config()->get_css_animation_config( false ),
		vc_config()->get_responsive_tab()
	),
];
