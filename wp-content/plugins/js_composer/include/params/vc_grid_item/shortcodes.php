<?php
/**
 * Shortcode attributes for vc_grid_item.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Gitem_Animated_Block' );

$vc_gitem_add_link_param = apply_filters( 'vc_gitem_add_link_param', [
	'type' => 'dropdown',
	'heading' => esc_html__( 'URL', 'js_composer' ),
	'param_name' => 'link',
	'value' => [
		esc_html__( 'None', 'js_composer' ) => 'none',
		esc_html__( 'Post link', 'js_composer' ) => 'post_link',
		esc_html__( 'Post author', 'js_composer' ) => 'post_author',
		esc_html__( 'Large image', 'js_composer' ) => 'image',
		esc_html__( 'Large image (prettyPhoto)', 'js_composer' ) => 'image_lightbox',
		esc_html__( 'Full image', 'js_composer' ) => 'image_full',
		esc_html__( 'Full image (prettyPhoto)', 'js_composer' ) => 'image_full_lightbox',
		esc_html__( 'Custom', 'js_composer' ) => 'custom',
	],
	'description' => esc_html__( 'Select link option.', 'js_composer' ),
	'edit_field_class' => 'vc_col-xs-6',
] );
$vc_gitem_add_link_target_param = apply_filters( 'vc_gitem_add_link_target_param', [
	'type' => 'toggle',
	'heading' => esc_html__( 'Open link in a new tab', 'js_composer' ),
	'param_name' => 'link_target',
	'description' => esc_html__( 'Select link target window.', 'js_composer' ),
	'dependency' => [
		'element' => 'link',
		'value_not_equal_to' => [ 'custom', 'none' ],
	],
] );
$zone_params = [
	$vc_gitem_add_link_param,
	$vc_gitem_add_link_target_param,
	[
		'type' => 'link',
		'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
		'param_name' => 'url',
		'dependency' => [
			'element' => 'link',
			'value' => [ 'custom' ],
		],
		'description' => esc_html__( 'Add custom link.', 'js_composer' ),
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Use featured image on background', 'js_composer' ),
		'param_name' => 'featured_image',
		'value' => [ 'yes' ],
		'std' => '',
		'description' => esc_html__( 'Note: Featured image overwrites background image and color from "Design Options".', 'js_composer' ),
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Image size', 'js_composer' ),
		'param_name' => 'img_size',
		'value' => 'large',
		'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'js_composer' ),
		'dependency' => [
			'element' => 'featured_image',
			'not_empty' => true,
		],
	],
	[
		'type' => 'css_editor',
		'param_name' => 'css',
		'group' => esc_html__( 'Design options', 'js_composer' ),
	],
	vc_config()->get_extra_class_params( false ),
];
$post_data_params = [
	$vc_gitem_add_link_param,
	$vc_gitem_add_link_target_param,
	[
		'type' => 'link',
		'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
		'param_name' => 'url',
		'dependency' => [
			'element' => 'link',
			'value' => [ 'custom' ],
		],
		'description' => esc_html__( 'Add custom link.', 'js_composer' ),
	],
	[
		'type' => 'css_editor',
		'param_name' => 'css',
		'group' => esc_html__( 'Design options', 'js_composer' ),
	],
];
$custom_fonts_width_checkbox_params = [
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Use custom font', 'js_composer' ),
		'param_name' => 'use_custom_fonts',
		'description' => esc_html__( 'Enable custom font option.', 'js_composer' ),
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'font_container',
		'param_name' => 'block_container',
		'value' => '',
		'settings' => [
			'fields' => [
				'font_size',
				'line_height',
				'text_align_description' => esc_html__( 'Select text alignment.', 'js_composer' ),
				'line_height_description' => esc_html__( 'Enter line height.', 'js_composer' ),
				'font_size_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-left',
				'line_height_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-right',
			],
		],
		'group' => esc_html__( 'Custom fonts', 'js_composer' ),
		'dependency' => [
			'element' => 'use_custom_fonts',
			'value' => [ 'yes' ],
		],
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Color', 'js_composer' ),
		'param_name' => 'color',
		'description' => esc_html__( 'Select color for your element.', 'js_composer' ),
		'group' => esc_html__( 'Custom fonts', 'js_composer' ),
		'dependency' => [
			'element' => 'use_custom_fonts',
			'value' => [ 'yes' ],
		],
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'toggle',
		'heading' => esc_html__( 'Use theme default', 'js_composer' ),
		'param_name' => 'use_theme_fonts',
		'description' => esc_html__( 'Yes font family from the theme.', 'js_composer' ),
		'group' => esc_html__( 'Custom fonts', 'js_composer' ),
		'dependency' => [
			'element' => 'use_custom_fonts',
			'value' => [ 'yes' ],
		],
		'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		'std' => '',
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'google_fonts',
		'param_name' => 'google_fonts',
		'value' => '',
		// Not recommended, this will override 'settings'.
		// 'font_family:'.rawurlencode('Exo:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic').'|font_style:'.rawurlencode('900 bold italic:900:italic').
		'settings' => [
			'fields' => [
				// Default font style. Name:weight:style, example: "800 bold regular:800:normal".
				'font_family_description' => esc_html__( 'Select font family.', 'js_composer' ),
				'font_style_description' => esc_html__( 'Select font styling.', 'js_composer' ),
				'font_family_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-left',
				'font_style_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-right',
			],
		],
		'group' => esc_html__( 'Custom fonts', 'js_composer' ),
		'dependency' => [
			'element' => 'use_theme_fonts',
			'value_not_equal_to' => 'yes',
		],
	],
];
$custom_fonts_params = array_merge(
	[
		[
			'type' => 'font_container',
			'param_name' => 'font_container',
			'value' => '',
			'settings' => [
				'fields' => [
					'tag' => 'div',
					// default value h2.
					'text_align',
					'tag_description' => esc_html__( 'Select element tag.', 'js_composer' ),
					'text_align_description' => esc_html__( 'Select text alignment.', 'js_composer' ),
					'font_size_description' => esc_html__( 'Enter font size.', 'js_composer' ),
					'line_height_description' => esc_html__( 'Enter line height.', 'js_composer' ),
					'color_description' => esc_html__( 'Select color for your element.', 'js_composer' ),
				],
			],
		],
	],
	$custom_fonts_width_checkbox_params,
);

$list = [];

$shortcodes_dir = __DIR__ . '/shortcodes/';
$shortcode_files = [
	'vc_gitem',
	'vc_gitem_animated_block',
	'vc_gitem_zone',
	'vc_gitem_zone_a',
	'vc_gitem_zone_b',
	'vc_gitem_zone_c',
	'vc_gitem_row',
	'vc_gitem_col',
	'vc_gitem_post_title',
	'vc_gitem_post_excerpt',
	'vc_gitem_post_author',
	'vc_gitem_post_categories',
	'vc_gitem_image',
	'vc_gitem_post_date',
	'vc_gitem_post_meta',
	'vc_column_text',
	'vc_separator',
	'vc_text_separator',
	'vc_icon',
	'vc_single_image',
	'vc_btn',
	'vc_custom_heading',
	'vc_empty_space',
];

foreach ( $shortcode_files as $shortcode_file ) {
	require $shortcodes_dir . $shortcode_file . '.php';
}

foreach ( $list as $key => $value ) {
	if ( isset( $list[ $key ]['params'] ) ) {
		$list[ $key ]['params'] = array_values( $list[ $key ]['params'] );
	}
}

return $list;
