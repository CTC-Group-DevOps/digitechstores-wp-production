<?php
/**
 * Configuration file for [vc_icon] shortcode of 'Icon' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Get shortcode attributes
 *
 * @return array
 */
function vc_icon_element_params() {
	$params = [
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Icon library', 'js_composer' ),
			'value' => vc_get_shared( 'icon libraries' ),
			'admin_label' => true,
			'param_name' => 'type',
			'section' => 'icons',
		],
		[
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'icon_fontawesome',
			'value' => 'fas fa-adjust',
			// default value to backend editor admin_label.
			'settings' => [
				'emptyIcon' => false,
				// default true, display an "EMPTY" icon.
				'iconsPerPage' => 500,
				// default 100, how many icons per/page to display, we use (big number) to display all icons in single page.
			],
			'dependency' => [
				'element' => 'type',
				'value' => 'fontawesome',
			],
			'section' => 'icons',
		],
		[
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'icon_openiconic',
			'value' => 'vc-oi vc-oi-dial',
			// default value to backend editor admin_label.
			'settings' => [
				'emptyIcon' => false,
				// default true, display an "EMPTY" icon.
				'type' => 'openiconic',
				'iconsPerPage' => 4000,
				// default 100, how many icons per/page to display.
			],
			'dependency' => [
				'element' => 'type',
				'value' => 'openiconic',
			],
			'section' => 'icons',
		],
		[
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'icon_typicons',
			'value' => 'typcn typcn-adjust-brightness',
			// default value to backend editor admin_label.
			'settings' => [
				'emptyIcon' => false,
				// default true, display an "EMPTY" icon.
				'type' => 'typicons',
				'iconsPerPage' => 4000,
				// default 100, how many icons per/page to display.
			],
			'dependency' => [
				'element' => 'type',
				'value' => 'typicons',
			],
			'section' => 'icons',
		],
		[
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'icon_entypo',
			'value' => 'entypo-icon entypo-icon-note',
			// default value to backend editor admin_label.
			'settings' => [
				'emptyIcon' => false,
				// default true, display an "EMPTY" icon.
				'type' => 'entypo',
				'iconsPerPage' => 4000,
				// default 100, how many icons per/page to display.
			],
			'dependency' => [
				'element' => 'type',
				'value' => 'entypo',
			],
			'section' => 'icons',
		],
		[
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'icon_linecons',
			'value' => 'vc_li vc_li-heart',
			// default value to backend editor admin_label.
			'settings' => [
				'emptyIcon' => false,
				// default true, display an "EMPTY" icon.
				'type' => 'linecons',
				'iconsPerPage' => 4000,
				// default 100, how many icons per/page to display.
			],
			'dependency' => [
				'element' => 'type',
				'value' => 'linecons',
			],
			'section' => 'icons',
		],
		[
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'icon_monosocial',
			'value' => 'vc-mono vc-mono-fivehundredpx',
			// default value to backend editor admin_label.
			'settings' => [
				'emptyIcon' => false,
				// default true, display an "EMPTY" icon.
				'type' => 'monosocial',
				'iconsPerPage' => 4000,
				// default 100, how many icons per/page to display.
			],
			'dependency' => [
				'element' => 'type',
				'value' => 'monosocial',
			],
			'section' => 'icons',
		],
		[
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'icon_material',
			'value' => 'vc-material vc-material-cake',
			// default value to backend editor admin_label.
			'settings' => [
				'emptyIcon' => false,
				// default true, display an "EMPTY" icon.
				'type' => 'material',
				'iconsPerPage' => 4000,
				// default 100, how many icons per/page to display.
			],
			'dependency' => [
				'element' => 'type',
				'value' => 'material',
			],
			'section' => 'icons',
		],
		[
			'type' => 'iconpicker',
			'heading' => esc_html__( 'Icon', 'js_composer' ),
			'param_name' => 'icon_pixelicons',
			'value' => 'vc_pixel_icon vc_pixel_icon-alert',
			'settings' => [
				'emptyIcon' => false,
				'type' => 'pixelicons',
				'source' => vc_get_shared( 'pixel icons' ),
			],
			'dependency' => [
				'element' => 'type',
				'value' => 'pixelicons',
			],
			'section' => 'icons',
		],

		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Size', 'js_composer' ),
			'param_name' => 'size',
			'value' => vc_config()->get_size_param_value(),
			'std' => 'md',
		],
		[
			'type' => 'button_group',
			'heading' => esc_html__( 'Alignment', 'js_composer' ),
			'param_name' => 'align',
			'value'       => vc_config()->get_text_align_param_value( [ 'justify' ] ),
			'std' => 'left',
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Color', 'js_composer' ),
			'param_name' => 'custom_color',
			'std' => '#5472D2',
			'settings' => [
				'default_colorpicker_color' => '#5472D2',
			],
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Background shape', 'js_composer' ),
			'param_name' => 'background_style',
			'value' => [
				esc_html__( 'None', 'js_composer' ) => '',
				esc_html__( 'Circle', 'js_composer' ) => 'rounded',
				esc_html__( 'Square', 'js_composer' ) => 'boxed',
				esc_html__( 'Rounded', 'js_composer' ) => 'rounded-less',
				esc_html__( 'Outline Circle', 'js_composer' ) => 'rounded-outline',
				esc_html__( 'Outline Square', 'js_composer' ) => 'boxed-outline',
				esc_html__( 'Outline Rounded', 'js_composer' ) => 'rounded-less-outline',
			],
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'colorpicker',
			'heading' => esc_html__( 'Background color', 'js_composer' ),
			'param_name' => 'custom_background_color',
			'dependency' => [
				'element' => 'background_style',
				'not_empty' => true,
			],
			'edit_field_class' => 'vc_col-xs-6',
		],
		[
			'type' => 'link',
			'heading' => esc_html__( 'Link', 'js_composer' ),
			'param_name' => 'link',
		],
	];

	return [
		'name' => esc_html__( 'Icon', 'js_composer' ),
		'base' => 'vc_icon',
		'icon' => 'icon-wpb-vc_icon',
		'element_default_class' => 'vc_do_icon',
		'category' => esc_html__( 'Content', 'js_composer' ),
		'description' => esc_html__( 'Eye catching icons from libraries', 'js_composer' ),
		'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
		'js_view' => 'VcIconElementView_Backend',
		'sections' => array_merge( [ 'icons' ], vc_config()->get_advanced_sections() ),
	];
}
