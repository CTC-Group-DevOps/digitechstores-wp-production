<?php
/**
 * Configuration file for [vc_message] shortcode of 'Message Box' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$pixel_icons = vc_get_shared( 'pixel icons' );

$params = [
	[
		'type' => 'textarea_html',
		'holder' => 'div',
		'class' => 'messagebox_text',
		'heading' => esc_html__( 'Content', 'js_composer' ),
		'param_name' => 'content',
		'value' => '<p>' . esc_html__( 'I am message box. Click edit button to change this text.', 'js_composer' ) . '</p>',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Presets', 'js_composer' ),
		'param_name' => 'color',
		// due to backward compatibility, really it is message_box_type.
		'value' => [
			esc_html__( 'Custom', 'js_composer' ) => '',
			esc_html__( 'Informational', 'js_composer' ) => 'info',
			esc_html__( 'Warning', 'js_composer' ) => 'warning',
			esc_html__( 'Success', 'js_composer' ) => 'success',
			esc_html__( 'Error', 'js_composer' ) => 'danger',
			esc_html__( 'Informational Classic', 'js_composer' ) => 'alert-info',
			esc_html__( 'Warning Classic', 'js_composer' ) => 'alert-warning',
			esc_html__( 'Success Classic', 'js_composer' ) => 'alert-success',
			esc_html__( 'Error Classic', 'js_composer' ) => 'alert-danger',
		],
		'description' => esc_html__( 'Select predefined message box design or choose "Custom" for custom styling.', 'js_composer' ),
		'param_holder_class' => 'vc_message-type vc_colored-dropdown',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Style', 'js_composer' ),
		'param_name' => 'message_box_style',
		'value' => vc_get_shared( 'message_box_styles' ),
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Shape', 'js_composer' ),
		'param_name' => 'style',
		// due to backward compatibility message_box_shape.
		'std' => 'rounded',
		'value' => [
			esc_html__( 'Square', 'js_composer' ) => 'square',
			esc_html__( 'Rounded', 'js_composer' ) => 'rounded',
			esc_html__( 'Round', 'js_composer' ) => 'round',
		],
		'edit_field_class' => 'vc_col-xs-6',
	],
	[
		'type' => 'colorpicker',
		'heading' => esc_html__( 'Color', 'js_composer' ),
		'param_name' => 'message_box_color',
		'dependency' => [
			'element' => 'color',
			'is_empty' => true,
		],
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Icon library', 'js_composer' ),
		'value' => vc_get_shared( 'icon libraries' ),
		'param_name' => 'icon_type',
		'dependency' => [
			'element' => 'color',
			'is_empty' => true,
		],
	],
	[
		'type' => 'iconpicker',
		'heading' => esc_html__( 'Icon', 'js_composer' ),
		'param_name' => 'icon_fontawesome',
		'value' => 'fa fa-solid fa-circle-info',
		'settings' => [
			'emptyIcon' => false,
			// default true, display an "EMPTY" icon.
			'iconsPerPage' => 500,
			// default 100, how many icons per/page to display.
		],
		'dependency' => [
			'element' => 'icon_type',
			'value' => 'fontawesome',
		],
	],
	[
		'type' => 'iconpicker',
		'heading' => esc_html__( 'Icon', 'js_composer' ),
		'param_name' => 'icon_openiconic',
		'value' => 'vc-oi vc-oi-dial',
		'settings' => [
			'emptyIcon' => false,
			// default true, display an "EMPTY" icon.
			'type' => 'openiconic',
			'iconsPerPage' => 4000,
			// default 100, how many icons per/page to display.
		],
		'dependency' => [
			'element' => 'icon_type',
			'value' => 'openiconic',
		],
	],
	[
		'type' => 'iconpicker',
		'heading' => esc_html__( 'Icon', 'js_composer' ),
		'param_name' => 'icon_typicons',
		'value' => 'typcn typcn-adjust-brightness',
		'settings' => [
			'emptyIcon' => false,
			// default true, display an "EMPTY" icon.
			'type' => 'typicons',
			'iconsPerPage' => 4000,
			// default 100, how many icons per/page to display.
		],
		'dependency' => [
			'element' => 'icon_type',
			'value' => 'typicons',
		],
	],
	[
		'type' => 'iconpicker',
		'heading' => esc_html__( 'Icon', 'js_composer' ),
		'param_name' => 'icon_entypo',
		'value' => 'entypo-icon entypo-icon-note',
		'settings' => [
			'emptyIcon' => false,
			// default true, display an "EMPTY" icon.
			'type' => 'entypo',
			'iconsPerPage' => 4000,
			// default 100, how many icons per/page to display.
		],
		'dependency' => [
			'element' => 'icon_type',
			'value' => 'entypo',
		],
	],
	[
		'type' => 'iconpicker',
		'heading' => esc_html__( 'Icon', 'js_composer' ),
		'param_name' => 'icon_linecons',
		'value' => 'vc_li vc_li-heart',
		'settings' => [
			'emptyIcon' => false,
			// default true, display an "EMPTY" icon.
			'type' => 'linecons',
			'iconsPerPage' => 4000,
			// default 100, how many icons per/page to display.
		],
		'dependency' => [
			'element' => 'icon_type',
			'value' => 'linecons',
		],
	],
	[
		'type' => 'iconpicker',
		'heading' => esc_html__( 'Icon', 'js_composer' ),
		'param_name' => 'icon_pixelicons',
		'value' => 'vc_pixel_icon vc_pixel_icon-alert',
		'settings' => [
			'emptyIcon' => false,
			// default true, display an "EMPTY" icon.
			'type' => 'pixelicons',
			'source' => $pixel_icons,
		],
		'dependency' => [
			'element' => 'icon_type',
			'value' => 'pixelicons',
		],
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
			'element' => 'icon_type',
			'value' => 'monosocial',
		],
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
			'element' => 'icon_type',
			'value' => 'material',
		],
	],
];

$design_options_defaults = [
	'padding-top' => '1em',
	'padding-bottom' => '1em',
	'padding-right' => '1em',
	'padding-left' => '4em',
	'margin-bottom' => '22px',
	'border-top-left-radius' => '5px',
	'border-top-right-radius' => '5px',
	'border-bottom-right-radius' => '5px',
	'border-bottom-left-radius' => '5px',
];

return [
	'name' => esc_html__( 'Message box', 'js_composer' ),
	'base' => 'vc_message',
	'icon' => 'icon-wpb-information-white',
	'element_default_class' => 'vc_do_message',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Notification box', 'js_composer' ),
	'params' => array_merge( $params, vc_config()->get_css_animation_config(), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab( $design_options_defaults ) ),
	'js_view' => 'VcMessageView_Backend',
];
