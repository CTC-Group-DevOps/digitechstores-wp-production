<?php
/**
 * Configuration file for [vc_pricing_table] shortcode of 'Pricing Table' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 * @since 7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-custom-heading-element.php' );
$heading_list = [
	'heading' => esc_html__( 'Heading', 'js_composer' ),
	'subheading' => esc_html__( 'Subheading', 'js_composer' ),
];

$heading_integration = [];
foreach ( $heading_list as $heading_name => $heading_title ) {
	$heading = vc_map_integrate_shortcode(
		vc_custom_heading_element_params(),
		$heading_name . '_',
		$heading_title,
		[
			'exclude' => [
				'source',
				'text',
				'css',
			],
		],
		[
			'element' => 'use_custom_fonts_' . $heading_name,
			'value' => 'true',
		],
		true,
	);

	// This is needed to remove custom heading _tag and _align options.
	if ( is_array( $heading ) && ! empty( $heading ) ) {
		foreach ( $heading as $key => $param ) {
			if ( is_array( $param ) && isset( $param['type'] ) && 'font_container' === $param['type'] ) {
				$heading[ $key ]['value'] = 'text_align:center';
			}
		}
	}

	$heading_integration[ $heading_name ] = $heading;
}

require_once vc_path_dir( 'CONFIG_DIR', 'content/vc-btn-element.php' );
$vc_btn_element_params = vc_btn_element_params();

// we change some predefined values.
$change_param_list = [
	'title' => [ 'value' => __( 'Get now', 'js_composer' ) ],
	'button_block' => [ 'std' => 'true' ],
	'align' => [ 'std' => 'center' ],
	'custom_background' => [ 'std' => '#0088CC' ],
	'custom_hover_background' => [ 'std' => '#0074AD' ],
	'custom_text' => [ 'std' => '#FFFFFF' ],
	'custom_hover_text' => [ 'std' => '#F7F7F7' ],
	'style' => [ 'std' => 'classic' ],
];

foreach ( $change_param_list as $param_name => $param_value ) {
	$key = array_search( $param_name, array_column( $vc_btn_element_params['params'], 'param_name' ) );

	if ( false === $key ) {
		continue;
	}

	$change_to = key( $param_value );
	$vc_btn_element_params['params'][ $key ][ $change_to ] = $param_value[ $change_to ];

	if ( 'button_block' === $param_name ) {
		$vc_btn_element_params['params'][ $key ]['value'] = [ 'Yes' => 'true' ];
	}
}

$button = vc_map_integrate_shortcode(
	$vc_btn_element_params,
	'btn_', esc_html__( 'Button', 'js_composer' ),
	[
		'title' => 'Get Now',
		'exclude' => [ 'css' ],
	],
	[
		'element' => 'add_button',
		'not_empty' => true,
	],
	true
);

$params = array_merge(
	[
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Heading', 'js_composer' ),
			'admin_label' => true,
			'param_name' => 'heading',
			'value' => esc_html__( 'Growth', 'js_composer' ),
			'description' => esc_html__( 'Enter text for heading line.', 'js_composer' ),
			'section' => 'general',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Use custom font', 'js_composer' ),
			'param_name' => 'use_custom_fonts_heading',
			'description' => esc_html__( 'Enable custom font option.', 'js_composer' ),
			'section' => 'general',
		],
	],
	$heading_integration['heading'],
	[
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Subheading', 'js_composer' ),
			'param_name' => 'subheading',
			'value' => 'For business',
			'description' => esc_html__( 'Enter text for subheading line.', 'js_composer' ),
			'section' => 'general',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Use custom font', 'js_composer' ),
			'param_name' => 'use_custom_fonts_subheading',
			'description' => esc_html__( 'Enable custom font option.', 'js_composer' ),
			'section' => 'general',
		],
	],
	$heading_integration['subheading'],
	[
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Price', 'js_composer' ),
			'param_name' => 'price',
			'value' => '99',
			'description' => esc_html__( 'Enter your price.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Currency', 'js_composer' ),
			'param_name' => 'currency',
			'value' => '$',
			'description' => esc_html__( 'Enter your price currency.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Period', 'js_composer' ),
			'param_name' => 'period',
			'value' => '/mo',
			'description' => esc_html__( 'Enter your price action period.', 'js_composer' ),
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
		],
		[
			'type' => 'toggle',
			'heading' => esc_html__( 'Add button', 'js_composer' ),
			'description' => esc_html__( 'Add button for call to action.', 'js_composer' ),
			'param_name' => 'add_button',
			'edit_field_class' => 'vc_col-xs-6',
			'section' => 'general',
			'value' => [ 'yes' ],
			'std' => 'yes',
		],
		[
			'type' => 'textarea_html',
			'heading' => esc_html__( 'Description', 'js_composer' ),
			'param_name' => 'content',
			'value' => wp_kses(
			'<ul class="wpb-plan-features">' .
						'<li>' . __( 'All premium features', 'js_composer' ) . '</li>' .
						'<li>' . __( 'Online support', 'js_composer' ) . '</li>' .
						'<li>' . __( 'Regular updates', 'js_composer' ) . '</li>' .
						'<li>' . __( 'Personal training', 'js_composer' ) . '</li>' .
					'</ul>',
					[
						'ul' => [ 'class' => [] ],
						'li' => [ 'class' => [] ],
					]
				),
			'section' => 'secondary',
		],
		[
			'type' => 'colorpicker',
			'value' => '#5188F1',
			'heading' => esc_html__( 'Markers color', 'js_composer' ),
			'param_name' => 'markers_color',
			'description' => esc_html__( 'Select custom color for your list markers.', 'js_composer' ),
			'section' => 'secondary',
		],
	],
	$button,
);

$default_values = [
	'padding-top' => '30px',
	'padding-right' => '20px',
	'padding-bottom' => '30px',
	'padding-left' => '20px',
	'border-top-left-radius' => '5px',
	'border-top-right-radius' => '5px',
	'border-bottom-right-radius' => '5px',
	'border-bottom-left-radius' => '5px',
	'border-top-width' => '1px',
	'border-right-width' => '1px',
	'border-bottom-width' => '1px',
	'border-left-width' => '1px',
	'border-style' => 'solid',
	'border-color' => 'rgba(0,0,0,0.01)',
	'background-color' => '#ECECEC',
];

return [
	'name' => esc_html__( 'Pricing table', 'js_composer' ),
	'base' => 'vc_pricing_table',
	'icon' => 'icon-wpb-pricing-table',
	'element_default_class' => 'vc_do_pricing_table',
	'category' => esc_html__( 'Content', 'js_composer' ),
	'description' => esc_html__( 'Output pricing table on your page', 'js_composer' ),
	'since' => '7.0',
	'params' => array_merge( $params, vc_config()->get_css_animation_config(), vc_config()->get_general_advanced_settings(), vc_config()->get_design_options_tab( $default_values ) ),
	'sections' => array_merge( [ 'general', 'secondary', 'content', 'container' ], vc_config()->get_advanced_sections() ),
];
