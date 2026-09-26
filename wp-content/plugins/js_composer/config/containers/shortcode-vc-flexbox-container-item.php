<?php
/**
 * Configuration file for [vc_flexbox_container_item] shortcode of 'Flexbox Item' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return [
	'name' => esc_html__( 'Flexbox item', 'js_composer' ),
	'icon' => 'icon-wpb-row',
	'is_container' => true,
	'content_element' => false,
	'show_settings_on_create' => false,
	'description' => esc_html__( 'Place content elements inside the flexbox item', 'js_composer' ),
	'params' => array_merge( vc_config()->get_css_animation_config( false ), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab() ),
	'js_view' => 'VcFlexboxContainerItemView',
];
