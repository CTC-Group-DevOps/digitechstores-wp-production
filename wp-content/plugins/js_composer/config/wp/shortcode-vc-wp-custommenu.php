<?php
/**
 * Configuration file for [vc_wp_custommenu] shortcode of 'WP Custom Menu' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$custom_menus = [];
if ( 'vc_edit_form' === vc_post_param( 'action' ) && vc_verify_admin_nonce() ) {
	// phpcs:ignore
	$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
	if ( is_array( $menus ) && ! empty( $menus ) ) {
		foreach ( $menus as $single_menu ) {
			if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->term_id ) ) {
				$custom_menus[ $single_menu->name ] = $single_menu->term_id;
			}
		}
	}
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'What text use as a widget title. Leave blank to use default widget title.', 'js_composer' ),
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Menu', 'js_composer' ),
		'param_name' => 'nav_menu',
		'value' => $custom_menus,
		'description' => empty( $custom_menus ) ? esc_html__( 'Custom menus not found. Please visit Appearance → Menus page to create new menu.', 'js_composer' ) : esc_html__( 'Select menu to display.', 'js_composer' ),
		'admin_label' => true,
		'save_always' => true,
	],
];

return [
	'name' => 'WP ' . esc_html__( 'custom menu' ),
	'base' => 'vc_wp_custommenu',
	'icon' => 'icon-wpb-wp',
	'category' => esc_html__( 'WordPress Widgets', 'js_composer' ),
	'class' => 'wpb_vc_wp_widget',
	'weight' => - 50,
	'description' => esc_html__( 'Use this widget to add one of your custom menus as a widget', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings() ),
];
