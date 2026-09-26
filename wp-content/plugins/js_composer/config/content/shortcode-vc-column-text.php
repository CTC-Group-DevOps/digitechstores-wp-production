<?php
/**
 * Configuration file for [vc_column_text] shortcode of 'Text block' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textarea_html',
		'holder' => 'div',
		'heading' => esc_html__( 'Text', 'js_composer' ),
		'param_name' => 'content',
		'value' => '<p>' . esc_html__( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'js_composer' ) . '</p>',
	],
];

return [
	'name' => esc_html__( 'Text block', 'js_composer' ),
	'icon' => 'icon-wpb-layer-shape-text',
	'wrapper_class' => 'clearfix',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'A block of text with WYSIWYG editor', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
];
