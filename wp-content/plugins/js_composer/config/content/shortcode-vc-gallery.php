<?php
/**
 * Configuration file for [vc_gallery] shortcode of 'Image gallery' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Image source', 'js_composer' ),
		'param_name' => 'source',
		'value' => [
			esc_html__( 'Media library', 'js_composer' ) => 'media_library',
			esc_html__( 'External links', 'js_composer' ) => 'external_link',
		],
		'std' => 'media_library',
		'description' => esc_html__( 'Select image source.', 'js_composer' ),
		'section' => 'content',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'On click action', 'js_composer' ),
		'param_name' => 'onclick',
		'value' => [
			esc_html__( 'None', 'js_composer' ) => '',
			esc_html__( 'Link to large image', 'js_composer' ) => 'img_link_large',
			esc_html__( 'Open Lightbox', 'js_composer' ) => 'link_image',
			esc_html__( 'Open custom link', 'js_composer' ) => 'custom_link',
		],
		'description' => esc_html__( 'Select action for click action.', 'js_composer' ),
		'std' => 'link_image',
		'dependency' => [
			'callback' => 'wpbGalleryOnClickDependency',
		],
		'section' => 'content',
	],
	[
		'type' => 'attach_images',
		'heading' => esc_html__( 'Images', 'js_composer' ),
		'param_name' => 'images',
		'value' => '',
		'description' => esc_html__( 'Select images from media library.', 'js_composer' ),
		'dependency' => [
			'element' => 'source',
			'value' => 'media_library',
		],
		'settings' => [
			'is_link_icon' => true,
		],
		'section' => 'content',
	],
	[
		'type' => 'exploded_textarea_safe',
		'heading' => esc_html__( 'External links', 'js_composer' ),
		'param_name' => 'custom_srcs',
		'description' => esc_html__( 'Enter external link for each gallery image (Note: divide links with linebreaks (Enter)).', 'js_composer' ),
		'dependency' => [
			'element' => 'source',
			'value' => 'external_link',
		],
		'section' => 'content',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Image size', 'js_composer' ),
		'param_name' => 'img_size',
		'value' => 'thumbnail',
		'description' => esc_html__( 'Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.', 'js_composer' ),
		'dependency' => [
			'element' => 'source',
			'value' => 'media_library',
		],
		'section' => 'content',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Image size', 'js_composer' ),
		'param_name' => 'external_img_size',
		'value' => '',
		'description' => esc_html__( 'Enter image size in pixels. Example: 200x100 (Width x Height).', 'js_composer' ),
		'dependency' => [
			'element' => 'source',
			'value' => 'external_link',
		],
		'section' => 'content',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Auto rotate', 'js_composer' ),
		'param_name' => 'interval',
		'value' => [
			3,
			5,
			10,
			15,
			esc_html__( 'Disable', 'js_composer' ) => 0,
		],
		'description' => esc_html__( 'Auto rotate slides each X seconds.', 'js_composer' ),
		'dependency' => [
			'element' => 'type',
			'value' => [
				'flexslider_fade',
				'flexslider_slide',
				'nivo',
			],
		],
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'content',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Gallery type', 'js_composer' ),
		'param_name' => 'type',
		'value' => [
			esc_html__( 'Flex slider fade', 'js_composer' ) => 'flexslider_fade',
			esc_html__( 'Flex slider slide', 'js_composer' ) => 'flexslider_slide',
			esc_html__( 'Nivo slider', 'js_composer' ) => 'nivo',
			esc_html__( 'Image grid', 'js_composer' ) => 'image_grid',
		],
		'description' => esc_html__( 'Select gallery type.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'content',
	],
	[
		'type' => 'exploded_textarea_safe',
		'heading' => esc_html__( 'Custom links', 'js_composer' ),
		'param_name' => 'custom_links',
		'description' => esc_html__( 'Enter links for each slide (Note: divide links with linebreaks (Enter)).', 'js_composer' ),
		'dependency' => [
			'callback' => 'wpbGalleryCustomLinksDependency',
		],
		'section' => 'options',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Custom link target', 'js_composer' ),
		'param_name' => 'custom_links_target',
		'description' => esc_html__( 'Select where to open  custom links.', 'js_composer' ),
		'dependency' => [
			'callback' => 'wpbGalleryCustomLinksTargetDependency',
		],
		'value' => vc_get_shared( 'target param list' ),
		'section' => 'options',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
		'section' => 'options',
	],
];

return [
	'name' => esc_html__( 'Image gallery', 'js_composer' ),
	'base' => 'vc_gallery',
	'icon' => 'icon-wpb-images-stack',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Responsive image gallery', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
	'sections' => array_merge( [ 'content', 'options' ], vc_config()->get_advanced_sections() ),
];
