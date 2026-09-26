<?php
/**
 * Library that helps to integrate element params configurations.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Wpb_Config_Lib
 *
 * @since 9.0
 */
class Wpb_Config_Lib {

	/**
	 * Get the general section slug.
	 *
	 * @since 9.0
	 * @return string
	 */
	public function get_general_section_slug() {
		return '_general';
	}

	/**
	 * Get the advanced section slug.
	 *
	 * @since 9.0
	 * @return string
	 */
	public function get_advanced_section_slug() {
		return '_advanced';
	}

	/**
	 * Get the design options CSS box section slug.
	 *
	 * @since 9.0
	 * @return string
	 */
	public function get_design_options_css_box_section_slug() {
		return '_design_options_css_box';
	}

	/**
	 * Get the design options animation section slug.
	 *
	 * @since 9.0
	 * @return string
	 */
	public function get_design_options_animation_section_slug() {
		return '_design_options_animation';
	}

	/**
	 * Get general advanced params.
	 *
	 * @since 9.0
	 * @return array
	 */
	public function get_general_advanced_settings() {
		return [
			$this->get_element_id_params(),
			$this->get_extra_class_params(),
		];
	}

	/**
	 * Get extra class settings.
	 *
	 * @param string|false $extra_class
	 * @return array
	 * @since 9.0
	 */
	public function get_extra_class_params( $extra_class = 'vc_col-xs-6' ) {
		$param = [
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
			'section' => $this->get_advanced_section_slug(),
		];

		if ( false !== $extra_class ) {
			$param['edit_field_class'] = $extra_class;
		}

		return $param;
	}

	/**
	 * Get element ID settings.
	 *
	 * @param string|false $extra_class
	 * @return array
	 * @since 9.0
	 */
	public function get_element_id_params( $extra_class = 'vc_col-xs-6' ) {
		$param = [
			'type' => 'el_id',
			'heading' => esc_html__( 'Element ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %1$sw3c specification%2$s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
			'section' => $this->get_advanced_section_slug(),
		];

		if ( false !== $extra_class ) {
			$param['edit_field_class'] = $extra_class;
		}

		return $param;
	}

	/**
	 * Get design options tab params.
	 *
	 * @since 9.0
	 * @param array $value
	 * @param array $extra_settings Optional per-element settings merged into the css param (e.g. background_style_default).
	 * @return array
	 */
	public function get_design_options_tab( $value = [], $extra_settings = [] ) {
		$return = [
			'type' => 'css_editor',
			'param_name' => 'css',
			'group' => esc_html__( 'Design options', 'js_composer' ),
			'section' => $this->get_design_options_css_box_section_slug(),
		];

		if ( $value ) {
			$return['value'] = $value;
		}

		if ( ! empty( $extra_settings ) && is_array( $extra_settings ) ) {
			$return = array_merge( $return, $extra_settings );
		}

		return [ $return ];
	}

	/**
	 * Get responsive tab configs.
	 *
	 * @since 9.0
	 * @return array
	 */
	public function get_responsive_tab() {
		return [
			[
				'type' => 'hidden',
				'param_name' => 'width',
				'std' => '1/1',
			],
			[
				'type' => 'column_offset',
				'param_name' => 'offset',
				'group' => esc_html__( 'Responsiveness', 'js_composer' ),
			],
		];
	}

	/**
	 * Merge default params to element params.
	 *
	 * @since 9.0
	 * @param array $params Element params.
	 * @param array $design_value
	 * @param array $design_settings Optional per-element settings merged into the css param.
	 * @return array
	 */
	public function merge_default_params( $params, $design_value = [], $design_settings = [] ) {
		return array_merge(
			$params,
			$this->get_css_animation_config(),
			$this->get_general_advanced_settings(),
			$this->get_design_options_tab( $design_value, $design_settings )
		);
	}

	/**
	 * Get css animation config.
	 *
	 * @since 9.0
	 * @param bool $is_admin_label
	 * @return array
	 */
	public function get_css_animation_config( $is_admin_label = true ) {
		$param = $this->get_css_animation( $is_admin_label );
		$param['group'] = esc_html__( 'Design options', 'js_composer' );
		$param['section'] = '_design_options_animation';

		return [ $param ];
	}

	/**
	 * Get raw css animation param without group or section.
	 *
	 * Use get_css_animation_config() instead for element configs, it adds the
	 * Design Options group and section. This method is used by deprecated elements
	 * and read by vc_map_integrate_shortcode from mapped shortcode data.
	 *
	 * @since 9.0
	 * @param bool $is_admin_label
	 * @return array
	 */
	public function get_css_animation( $is_admin_label = true ) {
		$data = [
			'type' => 'animation_style',
			'heading' => esc_html__( 'CSS animation', 'js_composer' ),
			'param_name' => 'css_animation',
			'admin_label' => $is_admin_label,
			'value' => '',
			'settings' => [
				'type' => 'in',
				'custom' => [
					[
						'label' => esc_html__( 'Default', 'js_composer' ),
						'values' => [
							esc_html__( 'Top to bottom', 'js_composer' ) => 'top-to-bottom',
							esc_html__( 'Bottom to top', 'js_composer' ) => 'bottom-to-top',
							esc_html__( 'Left to right', 'js_composer' ) => 'left-to-right',
							esc_html__( 'Right to left', 'js_composer' ) => 'right-to-left',
							esc_html__( 'Appear from center', 'js_composer' ) => 'appear',
						],
					],
				],
			],
		];

		return apply_filters( 'vc_map_add_css_animation', $data, $is_admin_label );
	}

	/**
	 * Attach the advanced section to params.
	 *
	 * @since 9.0
	 * @param array $settings
	 * @return array
	 */
	public function attach_advanced_section_to_params( $settings ) {
		$settings['sections'] = $this->get_advanced_sections();

		return $settings;
	}

	/**
	 * Get advanced sections.
	 *
	 * @since 9.0
	 * @return array
	 */
	public function get_advanced_sections() {
		return [
			$this->get_general_section_slug(),
			$this->get_design_options_css_box_section_slug(),
			$this->get_design_options_animation_section_slug(),
			$this->get_advanced_section_slug(),
		];
	}

	/**
	 * Get text align param value.
	 *
	 * @param array $exclude
	 * @since 9.0
	 * @return array
	 */
	public function get_text_align_param_value( $exclude = [] ) {
		$value = [
			'left'   => [
				'label' => 'vc-c-alignment-left',
				'title' => esc_html__( 'Left', 'js_composer' ),
			],
			'center'   => [
				'label' => 'vc-c-alignment-center',
				'title' => esc_html__( 'Center', 'js_composer' ),
			],
			'right'   => [
				'label' => 'vc-c-alignment-right',
				'title' => esc_html__( 'Right', 'js_composer' ),
			],
			'justify' => [
				'label' => 'vc-c-alignment',
				'title' => esc_html__( 'Justify', 'js_composer' ),
			],
		];

		if ( $exclude ) {
			foreach ( $exclude as $exclude_key ) {
				if ( isset( $value[ $exclude_key ] ) ) {
					unset( $value[ $exclude_key ] );
				}
			}
		}

		return $value;
	}

	/**
	 * Vertical position param value.
	 *
	 * @param array $exclude
	 * @since 9.0
	 * @return array
	 */
	public function get_vertical_position_param_value( $exclude = [] ) {
		$value = [
			'' => [
				'label' => 'vc-c-justify-vertical-between',
				'title' => esc_html__( 'Default', 'js_composer' ),
			],
			'top'   => [
				'label' => 'vc-c-justify-vertical-top',
				'title' => esc_html__( 'Top', 'js_composer' ),
			],
			'middle'   => [
				'label' => 'vc-c-justify-vertical-center',
				'title' => esc_html__( 'Middle', 'js_composer' ),
			],
			'bottom'   => [
				'label' => 'vc-c-justify-vertical-bottom',
				'title' => esc_html__( 'Bottom', 'js_composer' ),
			],
		];

		if ( $exclude ) {
			foreach ( $exclude as $exclude_key ) {
				if ( isset( $value[ $exclude_key ] ) ) {
					unset( $value[ $exclude_key ] );
				}
			}
		}

		return $value;
	}

	/**
	 * Get size param value.
	 *
	 * @param array $accepted_value_list
	 * @since 9.0
	 * @return array
	 */
	public function get_size_param_value( $accepted_value_list = [] ) {
		$value = [
			'xs' => [
				'label' => esc_html__( 'XS', 'js_composer' ),
				'title' => esc_html__( 'Extra Small', 'js_composer' ),
			],
			'sm' => [
				'label' => esc_html__( 'S', 'js_composer' ),
				'title' => esc_html__( 'Small', 'js_composer' ),
			],
			'md' => [
				'label' => esc_html__( 'M', 'js_composer' ),
				'title' => esc_html__( 'Medium', 'js_composer' ),
			],
			'lg' => [
				'label' => esc_html__( 'L', 'js_composer' ),
				'title' => esc_html__( 'Large', 'js_composer' ),
			],
			'xl' => [
				'label' => esc_html__( 'XL', 'js_composer' ),
				'title' => esc_html__( 'Extra Large', 'js_composer' ),
			],
		];

		if ( $accepted_value_list ) {
			foreach ( $value as $value_key => $value_item ) {
				if ( in_array( $value_key, $accepted_value_list, true ) ) {
					continue;
				}

				unset( $value[ $value_key ] );
			}
		}

		return $value;
	}
}
