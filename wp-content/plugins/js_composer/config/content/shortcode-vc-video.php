<?php
/**
 * Configuration file for [vc_video] shortcode of 'Video Player' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$params = [
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Video URL', 'js_composer' ),
		'param_name' => 'link',
		'value' => 'https://vimeo.com/channels/staffpicks/181907337',
		'admin_label' => true,
		'description' => sprintf( esc_html__( 'Enter link to video (Note: read more about available formats at WordPress %1$scodex page%2$s).', 'js_composer' ), '<a href="https://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">', '</a>' ),
		'section' => 'video',
	],
	[
		'type' => 'range',
		'heading' => esc_html__( 'Width', 'js_composer' ),
		'param_name' => 'el_width',
		'value' => '100',
		'settings' => [
			'min' => '0',
			'max' => '100',
			'step' => '1',
			'unit' => '%',
		],
		'description' => esc_html__( 'Select video width (percentage).', 'js_composer' ),
		'section' => 'video',
	],
	[
		'type' => 'dropdown',
		'heading' => esc_html__( 'Aspect ratio', 'js_composer' ),
		'param_name' => 'el_aspect',
		'value' => [
			'16:9' => '169',
			'4:3' => '43',
			'2.35:1' => '235',
			'9:16' => '916',
			'3:4' => '34',
			'1:2.35' => '1235',
		],
		'description' => esc_html__( 'Select video aspect ratio.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'video',
	],
	[
		'type' => 'button_group',
		'heading' => esc_html__( 'Alignment', 'js_composer' ),
		'param_name' => 'align',
		'value'       => vc_config()->get_text_align_param_value( [ 'justify' ] ),
		'std' => 'left',
		'description' => esc_html__( 'Select video alignment.', 'js_composer' ),
		'edit_field_class' => 'vc_col-xs-6',
		'section' => 'video',
	],
	[
		'type' => 'textfield',
		'heading' => esc_html__( 'Widget title', 'js_composer' ),
		'param_name' => 'title',
		'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
	],
];

return [
	'name' => esc_html__( 'Video player', 'js_composer' ),
	'base' => 'vc_video',
	'icon' => 'icon-wpb-film-youtube',
	'element_default_class' => 'wpb_content_element',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Embed YouTube/Vimeo player', 'js_composer' ),
	'params' => vc_config()->merge_default_params( $params, [ 'margin-bottom' => '35px' ] ),
	'sections' => array_merge( [ 'video' ], vc_config()->get_advanced_sections() ),
];
