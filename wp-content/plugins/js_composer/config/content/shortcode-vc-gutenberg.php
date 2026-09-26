<?php
/**
 * Backward compatibility gutenberg native wp editor.
 *
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'gutenberg',
		'holder' => 'div',
		'param_name' => 'content',
		'value' => '<!-- wp:paragraph --><p>Hello! This is the Gutenberg block you can edit directly from the WPBakery Page Builder.</p><!-- /wp:paragraph -->',
	],
	[
		'type' => 'hidden',
		'value' => 'false',
		'param_name' => 'do_blocks',
		'description' => esc_html__( 'Render Gutenberg blocks directly in shortcode templates.', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Gutenberg editor', 'js_composer' ),
	'icon' => 'vc_icon-vc-gutenberg',
	'wrapper_class' => 'clearfix',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Insert Gutenberg editor in your layout', 'js_composer' ),
	'weight' => - 10,
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
];
