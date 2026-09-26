<?php
/**
 * Configuration file for [vc_custom_heading] shortcode of 'Custom Heading' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 *
 * @param array $font_container_fields since 9.0.
 *
 * @return array
 */
function vc_custom_heading_element_params( $font_container_fields = [] ) {
	if ( ! $font_container_fields ) {
		$font_container_fields = [
			'tag' => 'h2',
			'font_size',
			'line_height',
			'color',
			'text_align',
			'default_colorpicker_color' => '#111111',
			'tag_description' => esc_html__( 'Select element tag.', 'js_composer' ),
			'text_align_description' => esc_html__( 'Select text alignment.', 'js_composer' ),
			'font_size_description' => esc_html__( 'Enter font size.', 'js_composer' ),
			'line_height_description' => esc_html__( 'Enter line height.', 'js_composer' ),
			'color_description' => esc_html__( 'Select heading color.', 'js_composer' ),
			'tag_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-left',
			'font_size_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-right',
			'line_height_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-left',
			'color_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-right wpb-min-height-field',
			'text_align_edit_field_class' => 'vc_col-xs-12',
		];
	}

	$params = [
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Source', 'js_composer' ),
			'param_name' => 'source',
			'value' => [
				esc_html__( 'Custom text', 'js_composer' ) => '',
				esc_html__( 'Post or Page Title', 'js_composer' ) => 'post_title',
			],
			'std' => '',
			'description' => esc_html__( 'Select text source.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'content',
		],
		[
			'type' => 'textarea',
			'heading' => esc_html__( 'Content', 'js_composer' ),
			'param_name' => 'text',
			'admin_label' => true,
			'value' => esc_html__( 'This is custom heading element', 'js_composer' ),
			'description' => esc_html__( 'Note: If you are using non-latin characters be sure to activate them under Settings/WPBakery Page Builder/General Settings.', 'js_composer' ),
			'dependency' => [
				'element' => 'source',
				'is_empty' => true,
			],
			'section' => 'content',
		],
		[
			'type' => 'link',
			'heading' => esc_html__( 'Post or page link', 'js_composer' ),
			'param_name' => 'link',
			'description' => esc_html__( 'Add link to custom heading.', 'js_composer' ),
			'section' => 'content',
		],
		[
			'type' => 'font_container',
			'param_name' => 'font_container',
			'value' => 'tag:h2|text_align:left',
			'settings' => [
				'fields' => $font_container_fields,
			],
			'section' => 'container',
		],

		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Use theme default font family', 'js_composer' ),
			'param_name' => 'use_theme_fonts',
			'description' => esc_html__( 'Use font family from the theme.', 'js_composer' ),
			'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
			'std' => '',
		],
		[
			'type' => 'google_fonts',
			'param_name' => 'google_fonts',
			'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
			'settings' => [
				'fields' => [
					// Default font style. Name:weight:style, example: "800 bold regular:800:normal".
					'font_family_description' => esc_html__( 'Select font family.', 'js_composer' ),
					'font_style_description' => esc_html__( 'Select font styling.', 'js_composer' ),
					'font_family_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-left',
					'font_style_edit_field_class' => 'vc_col-xs-6 wpb-half-width-field-right',
				],
			],
			'dependency' => [
				'element' => 'use_theme_fonts',
				'value_not_equal_to' => 'yes',
			],
		],
	];

	$design_options_defaults = [
		'margin-bottom' => '0.625rem',
		'margin-top' => '0',
	];

	return [
		'name' => esc_html__( 'Custom heading', 'js_composer' ),
		'base' => 'vc_custom_heading',
		'icon' => 'icon-wpb-ui-custom_heading',
		'element_default_class' => 'vc_do_custom_heading',
		'show_settings_on_create' => true,
		'category' => esc_html__( 'Content', 'js_composer' ),
		'description' => esc_html__( 'Text with custom fonts', 'js_composer' ),
		'params' => array_merge( $params, vc_config()->get_css_animation_config(), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab( $design_options_defaults ) ),
		'sections' => array_merge( [ 'content', 'container' ], vc_config()->get_advanced_sections() ),
	];
}
