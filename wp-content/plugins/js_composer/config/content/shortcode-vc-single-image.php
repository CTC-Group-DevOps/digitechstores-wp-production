<?php
/**
 * Configuration file for [vc_single_image] shortcode of 'Single image' element.
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
			esc_html__( 'External link', 'js_composer' ) => 'external_link',
			esc_html__( 'Featured Image', 'js_composer' ) => 'featured_image',
		],
		'std' => 'media_library',
		'section' => 'content',
	],
	// backward compatibility. since 4.6.
	[
		'type' => 'hidden',
		'param_name' => 'img_link_large',
		'section' => 'content',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'External link', 'js_composer' ),
		'param_name' => 'custom_src',
		'description' => esc_html__( 'Select external link.', 'js_composer' ),
		'dependency' => [
			'element' => 'source',
			'value' => 'external_link',
		],
		'admin_label' => true,
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
			esc_html__( 'Zoom', 'js_composer' ) => 'zoom',
		],
		'std' => '',
		'dependency' => [
			'callback' => 'wpbSingleImageOnClickDependency',
		],
		'section' => 'content',
	],
	[
		'type' => 'link',
		'heading' => esc_html__( 'Image link', 'js_composer' ),
		'param_name' => 'link',
		'description' => esc_html__( 'Enter URL if you want this image to have a link (Note: parameters like "mailto:" are also accepted).', 'js_composer' ),
		'settings' => [
			'is_title' => false,
		],
		'dependency' => [
			'callback' => 'wpbSingleImageOnClickDependency',
		],
		'section' => 'content',
	],
	[
		'type' => 'attach_image',
		'heading' => esc_html__( 'Image', 'js_composer' ),
		'param_name' => 'image',
		'value' => '',
		'settings' => [
			'is_link_icon' => true,
		],
		'dependency' => [
			'element' => 'source',
			'value' => 'media_library',
		],
		'admin_label' => true,
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'content',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Add caption', 'js_composer' ),
		'param_name' => 'add_caption',
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		'std' => '',
		'dependency' => [
			'element' => 'source',
			'value' => [
				'media_library',
				'featured_image',
			],
		],
		'edit_field_class' => 'vc_col-xs-6',
		'param_holder_class' => 'wpb-single-image-add-caption vc_align-with-labeled-field--top',
		'section' => 'content',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Caption', 'js_composer' ),
		'param_name' => 'caption',
		'description' => esc_html__( 'Enter text for image caption.', 'js_composer' ),
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
		'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'js_composer' ),
		'dependency' => [
			'element' => 'source',
			'value' => [
				'media_library',
				'featured_image',
			],
		],
		'edit_field_class' => 'vc_col-xs-6',
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
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'content',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Image style', 'js_composer' ),
		'param_name' => 'style',
		'value' => vc_get_shared( 'single image styles' ),
		'dependency' => [
			'element' => 'source',
			'value' => [
				'media_library',
				'featured_image',
			],
		],
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'content',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Image style', 'js_composer' ),
		'param_name' => 'external_style',
		'value' => vc_get_shared( 'single image external styles' ),
		'description' => esc_html__( 'Select image display style.', 'js_composer' ),
		'dependency' => [
			'element' => 'source',
			'value' => 'external_link',
		],
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'content',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Border color', 'js_composer' ),
		'param_name' => 'border_color',
		'std' => '',
		'settings' => [
			'default_colorpicker_color' => '#EBEBEB',
		],
		'dependency' => [
			'element' => 'style',
			'value' => [
				'vc_box_border',
				'vc_box_border_circle',
				'vc_box_outline',
				'vc_box_outline_circle',
				'vc_box_border_circle_2',
				'vc_box_outline_circle_2',
			],
		],
		'description' => esc_html__( 'Border color.', 'js_composer' ),
		'section' => 'content',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Border color', 'js_composer' ),
		'param_name' => 'external_border_color',
		'std' => '',
		'settings' => [
			'default_colorpicker_color' => '#EBEBEB',
		],
		'dependency' => [
			'element' => 'external_style',
			'value' => [
				'vc_box_border',
				'vc_box_border_circle',
				'vc_box_outline',
				'vc_box_outline_circle',
			],
		],
		'description' => esc_html__( 'Border color.', 'js_composer' ),
		'section' => 'content',
	],
	[
		'type' => 'button_group',
		'heading' => esc_html__( 'Alignment', 'js_composer' ),
		'param_name' => 'alignment',
		'value'       => vc_config()->get_text_align_param_value(),
		'std' => 'left',
		'section' => 'content',
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
	'name' => esc_html__( 'Single image', 'js_composer' ),
	'base' => 'vc_single_image',
	'icon' => 'icon-wpb-single-image',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Simple image with CSS animation', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
	'sections' => array_merge( [ 'content', 'options' ], vc_config()->get_advanced_sections() ),
];
