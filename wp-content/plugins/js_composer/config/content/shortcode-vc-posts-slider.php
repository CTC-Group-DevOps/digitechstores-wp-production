<?php
/**
 * Configuration file for [vc_posts_slider] shortcode of 'Posts Slider' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Slider type', 'js_composer' ),
		'param_name' => 'type',
		'admin_label' => true,
		'value' => [
			esc_html__( 'Flex slider fade', 'js_composer' ) => 'flexslider_fade',
			esc_html__( 'Flex slider slide', 'js_composer' ) => 'flexslider_slide',
			esc_html__( 'Nivo slider', 'js_composer' ) => 'nivo',
		],
		'description' => esc_html__( 'Select slider type.', 'js_composer' ),
		'section' => 'general',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Number of items', 'js_composer' ),
		'param_name' => 'count',
		'value' => 3,
		'description' => esc_html__( 'Enter number of slides to display.', 'js_composer' ),
		'settings' => [
			'min' => 0,
		],
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'number',
		'heading' => esc_html__( 'Auto rotate', 'js_composer' ),
		'param_name' => 'interval',
		'value' => 3,
		'settings' => [
			'min' => 0,
		],
		'description' => esc_html__( 'Auto rotate slides each X seconds. Set 0 to disable auto rotate.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Thumbnail size', 'js_composer' ),
		'param_name' => 'thumb_size',
		'value' => 'medium',
		'description' => esc_html__( 'Enter thumbnail size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height) . ', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'general',
	],
	[
		'type' => 'posttypes',
		'heading' => esc_html__( 'Post types', 'js_composer' ),
		'param_name' => 'posttypes',
		'description' => esc_html__( 'Select source for slider.', 'js_composer' ),
		'section' => 'types',
	],

	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Description', 'js_composer' ),
		'param_name' => 'slides_content',
		'value' => [
			esc_html__( 'No description', 'js_composer' ) => '',
			esc_html__( 'Teaser (Excerpt)', 'js_composer' ) => 'teaser',
		],
		'description' => esc_html__( 'Select source to use for description (Note: some sliders do not support it).', 'js_composer' ),
		'dependency' => [
			'element' => 'type',
			'value' => [
				'flexslider_fade',
				'flexslider_slide',
			],
		],
		'section' => 'description',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Output post title', 'js_composer' ),
		'param_name' => 'slides_title',
		'description' => esc_html__( 'If selected, title will be printed before the teaser text.', 'js_composer' ),
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => true ],
		'std' => '',
		'dependency' => [
			'element' => 'slides_content',
			'value' => [ 'teaser' ],
		],
		'section' => 'description',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Link', 'js_composer' ),
		'param_name' => 'link',
		'value' => [
			esc_html__( 'Link to post', 'js_composer' ) => 'link_post',
			esc_html__( 'Link to bigger image', 'js_composer' ) => 'link_image',
			esc_html__( 'Open custom links', 'js_composer' ) => 'custom_link',
			esc_html__( 'No link', 'js_composer' ) => 'link_no',
		],
		'description' => esc_html__( 'Link type.', 'js_composer' ),
		'section' => 'description',
	],
	[
		'type' => 'exploded_textarea_safe',
		'heading' => esc_html__( 'Custom links', 'js_composer' ),
		'param_name' => 'custom_links',
		'value' => site_url() . '/',
		'dependency' => [
			'element' => 'link',
			'value' => 'custom_link',
		],
		'description' => esc_html__( 'Enter links for each slide here. Divide links with linebreaks (Enter).', 'js_composer' ),
		'section' => 'description',
	],

	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Post/page IDs', 'js_composer' ),
		'param_name' => 'posts_in',
		'description' => esc_html__( 'Enter page/posts IDs to display only those records (Note: separate values by commas (,)). Use this field in conjunction with "Post types" field.', 'js_composer' ),
		'section' => 'categories',
	],
	[
		'type' => 'exploded_textarea_safe',
		'heading' => esc_html__( 'Categories', 'js_composer' ),
		'param_name' => 'categories',
		'description' => esc_html__( 'Enter categories by names to narrow output (Note: only listed categories will be displayed, divide categories with linebreak (Enter)).', 'js_composer' ),
		'section' => 'categories',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Order by', 'js_composer' ),
		'param_name' => 'orderby',
		'value' => [
			'',
			esc_html__( 'Date', 'js_composer' ) => 'date',
			esc_html__( 'ID', 'js_composer' ) => 'ID',
			esc_html__( 'Author', 'js_composer' ) => 'author',
			esc_html__( 'Title', 'js_composer' ) => 'title',
			esc_html__( 'Modified', 'js_composer' ) => 'modified',
			esc_html__( 'Random', 'js_composer' ) => 'rand',
			esc_html__( 'Comment count', 'js_composer' ) => 'comment_count',
			esc_html__( 'Menu order', 'js_composer' ) => 'menu_order',
		],
		'description' => sprintf( esc_html__( 'Select how to sort retrieved posts. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'categories',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Sort order', 'js_composer' ),
		'param_name' => 'order',
		'value' => [
			esc_html__( 'Descending', 'js_composer' ) => 'DESC',
			esc_html__( 'Ascending', 'js_composer' ) => 'ASC',
		],
		'description' => sprintf( esc_html__( 'Select ascending or descending order. More at %s.', 'js_composer' ), '<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'categories',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Posts slider', 'js_composer' ),
	'base' => 'vc_posts_slider',
	'icon' => 'icon-wpb-slideshow',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Slider with WP Posts', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab( [ 'margin-bottom' => '35px' ] ), vc_config()->get_css_animation_config() ),
	'sections' => array_merge( [ 'general', 'types', 'description', 'categories' ], vc_config()->get_advanced_sections() ),
];
