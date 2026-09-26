<?php
/**
 * Configuration file for [vc_copyright] shortcode of 'Copyright' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 *
 * @since 8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Prefix', 'js_composer' ),
		'value' => esc_html__( 'Copyright ', 'js_composer' ),
		'param_name' => 'prefix',
		'admin_label' => true,
		'description' => esc_html__( 'Text in the beginning', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Postfix', 'js_composer' ),
		'value' => esc_html__( ' All rights reserved', 'js_composer' ),
		'param_name' => 'postfix',
		'admin_label' => true,
		'description' => esc_html__( 'Text in the end', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'button_group',
		'heading' => esc_html__( 'Alignment', 'js_composer' ),
		'param_name' => 'align',
		'value'       => vc_config()->get_text_align_param_value( [ 'justify' ] ),
		'std' => 'left',
	],
];

return [
	'name' => esc_html__( 'Copyright', 'js_composer' ),
	'base' => 'vc_copyright',
	'icon' => 'icon-wpb-copyright',
	'element_default_class' => 'wpb_copyright_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Copyright with dynamic year', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
];
