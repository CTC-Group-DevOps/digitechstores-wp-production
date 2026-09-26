<?php
/**
 * WPBakery Page Builder shortcode default attributes functions for rendering.
 *
 * @package WPBakeryPageBuilder
 * @since 4.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! function_exists( 'vc_textfield_form_field' ) ) :
	/**
	 * Textfield shortcode attribute type generator.
	 *
	 * @since 4.4
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 * @return string - html string.
	 */
	function vc_textfield_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$param_type = $settings['type'];
		$param_name = $settings['param_name'];
		// $settings['value_type'] b.c prior to 9.0
		$value_type = $settings['settings']['value_type'] ?? $settings['value_type'] ?? 'html';
		// $settings['placeholder'] b.c prior to 9.0
		$placeholder = $settings['settings']['placeholder'] ?? $settings['placeholder'] ?? '';

		return WPB_Form_Field_Textfield::get(
			[
				'id'          => wpbakery()->editForm()->get_value_control_id( $param_id, $param_type ),
				'classes'     => wpbakery()->editForm()->get_value_control_classes( $param_name, $param_type ),
				'name'        => $param_name,
				'value'       => is_string( $value ) || is_numeric( $value ) ? nl2br( htmlspecialchars( (string) $value ) ) : '',
				'value_type'  => $value_type,
				'placeholder' => $placeholder,
			]
		);
	}
endif;

if ( ! function_exists( 'vc_checkbox_form_field' ) ) :
	/**
	 * Checkbox shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_checkbox_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		if ( is_array( $value ) || is_null( $value ) ) {
			$value = ''; // fix #1239.
		}
		$current_value = strlen( $value ) > 0 ? explode( ',', $value ) : [];
		$direction = isset( $settings['settings']['direction'] ) && 'vertical' === $settings['settings']['direction'] ? 'vertical' : 'horizontal';
		$options = isset( $settings['value'] ) && is_array( $settings['value'] ) ? $settings['value'] : [ esc_html__( 'Yes', 'js_composer' ) => 'true' ];
		$heading = isset( $settings['heading'] ) ? $settings['heading'] : '';

		return vc_get_template( 'params/checkbox/template.php', [
			'settings' => $settings,
			'value' => $value,
			'param_id' => $param_id,
			'heading' => $heading,
			'options' => $options,
			'direction' => $direction,
			'current_value' => $current_value,
		] );
	}
endif;

if ( ! function_exists( 'vc_posttypes_form_field' ) ) :
	/**
	 * Checkbox shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_posttypes_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$args = [
			'public' => true,
		];
		$post_types = get_post_types( $args );
		$value = is_string( $value ) ? $value : '';
		$current_value = strlen( $value ) > 0 ? explode( ',', $value ) : [];
		$input_attr_class = wpbakery()->editForm()->get_value_control_attr_class( $settings['param_name'], $settings['type'] );
		$direction = isset( $settings['settings']['direction'] ) ? $settings['settings']['direction'] : 'horizontal';
		$heading = isset( $settings['heading'] ) ? $settings['heading'] : esc_html__( 'Post types', 'js_composer' );

		unset( $post_types['attachment'] );
		unset( $post_types['wpb_gutenberg_param'] );

		return vc_get_template( 'params/checkbox/template.php', [
			'settings' => $settings,
			'value' => $value,
			'heading' => $heading,
			'options' => $post_types,
			'direction' => $direction,
			'param_id' => $param_id,
			'current_value' => $current_value,
			'input_attr_class' => $input_attr_class,
		] );
	}
endif;

if ( ! function_exists( 'vc_taxonomies_form_field' ) ) :
	/**
	 * Taxonomies shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_taxonomies_form_field( $settings, $value ) {
		$output = '';
		$post_types = get_post_types( [
			'public' => false,
			'name' => 'attachment',
		], 'names', 'NOT' );
		$value = is_string( $value ) ? $value : '';
		$input_attr_class = wpbakery()->editForm()->get_value_control_attr_class( $settings['param_name'], $settings['type'] );
		$direction = isset( $settings['settings']['direction'] ) ? $settings['settings']['direction'] : 'horizontal';
		$heading = isset( $settings['heading'] ) ? $settings['heading'] : esc_html__( 'Taxonomies', 'js_composer' );

		$output .= '<div class="wpb_checkbox-container wpb_checkbox-container-' . esc_attr( $direction ) . '" role="group" aria-label="' . esc_attr( $heading ) . '">';

		foreach ( $post_types as $type ) {
			$taxonomies = get_object_taxonomies( $type, '' );
			foreach ( $taxonomies as $tax ) {
				$is_checked = in_array( $tax->name, explode( ',', $value ), true );

				$output .= WPB_Form_Field_Checkbox::get( [
					'id'               => $settings['param_name'] . '-' . $tax->name,
					'name'             => $settings['param_name'],
					'value'            => $tax->name,
					'checked'          => $is_checked,
					'label'            => $tax->label,
					'input_attr_class' => $input_attr_class,
					'data_attributes'  => [ 'post-type' => $type ],
					'label_attributes' => [ 'post-type' => $type ],
				] );
			}
		}

		$output .= '</div>';

		return $output;
	}
endif;

if ( ! function_exists( 'vc_exploded_textarea_form_field' ) ) :
	/**
	 * Exploded textarea shortcode attribute type generator.
	 *
	 * Data saved and coma-separated values are merged with line breaks and returned in a textarea.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_exploded_textarea_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$value = is_string( $value ) ? $value : '';
		$value = str_replace( ',', "\n", $value );

		return WPB_Form_Field_Textarea::get( [
			'name'  => $settings['param_name'],
			'value' => $value,
			'id'    => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
			'class' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] ),
		] );
	}
endif;

if ( ! function_exists( 'vc_exploded_textarea_safe_form_field' ) ) :
	/**
	 * Safe Textarea shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.8.2
	 */
	function vc_exploded_textarea_safe_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$value = vc_value_from_safe( $value, true );
		if ( $value ) {
			$value = str_replace( ',', "\n", $value );
		}

		return WPB_Form_Field_Textarea::get( [
			'name'  => $settings['param_name'],
			'value' => $value,
			'id'    => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
			'class' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] ),
		] );
	}
endif;

if ( ! function_exists( 'vc_range_form_field' ) ) :
	/**
	 * Range input and number input shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param string $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string
	 */
	function vc_range_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		return WPB_Form_Field_Range::get( [
			'name'     => $settings['param_name'],
			'value'    => $value,
			'min'      => $settings['settings']['min'] ?? '',
			'max'      => $settings['settings']['max'] ?? '',
			'step'     => $settings['settings']['step'] ?? '',
			'settings' => $settings,
			'param_id' => $param_id,
			'placeholder' => $settings['settings']['placeholder'] ?? '',
			'unit'     => $settings['settings']['unit'] ?? '',
		] );
	}
endif;

if ( ! function_exists( 'vc_textarea_raw_html_form_field' ) ) :
	/**
	 * Textarea raw html shortcode attribute type generator.
	 *
	 * This attribute type allows safely add custom html to your post/page.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_textarea_raw_html_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$value = is_string( $value ) ? $value : '';
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$value = htmlentities( rawurldecode( base64_decode( $value ) ), ENT_COMPAT, 'UTF-8' );

		return WPB_Form_Field_Textarea::get( [
			'name'  => $settings['param_name'],
			'value' => $value,
			'rows'  => '16',
			'id'    => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
			'class' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'], '', 'wpb-textarea_raw_html' ),
		] );
	}
endif;

if ( ! function_exists( 'vc_textarea_safe_form_field' ) ) :
	/**
	 * Safe Textarea shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_textarea_safe_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		return WPB_Form_Field_Textarea::get( [
			'name'  => $settings['param_name'],
			'value' => vc_value_from_safe( $value, true ),
			'id'    => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
			'class' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'], '', 'wpb-textarea_raw_html' ),
		] );
	}
endif;

if ( ! function_exists( 'vc_textarea_form_field' ) ) :
	/**
	 * Textarea shortcode attribute type generator.
	 * Textarea param type is used for large plain text input.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_textarea_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		// $settings['value_type'] b.c prior to 9.0
		$value_type = $settings['settings']['value_type'] ?? $settings['value_type'] ?? 'html';
		// $settings['placeholder'] b.c prior to 9.0
		$placeholder = $settings['settings']['placeholder'] ?? $settings['placeholder'] ?? '';

		return WPB_Form_Field_Textarea::get( [
			'name'        => $settings['param_name'],
			'value'       => $value,
			'placeholder' => $placeholder,
			'value_type'  => $value_type,
			'id'          => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
			'class'       => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'], '', 'wpb-textarea' ),
		] );
	}
endif;

if ( ! function_exists( 'vc_attach_images_form_field' ) ) :
	/**
	 * Attach images shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_attach_images_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$param = new WpbParamAttachImages();
		return $param->render( $settings, $value, $tag, $param_id );
	}
endif;

if ( ! function_exists( 'vc_attach_image_form_field' ) ) :
	/**
	 * Attach image shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_attach_image_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$link_param = new WpbParamAttachImage();
		return $link_param->render( $settings, $value, $tag, $param_id );
	}
endif;

if ( ! function_exists( 'vc_widgetised_sidebars_form_field' ) ) :
	/**
	 * Widgetised sidebars shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_widgetised_sidebars_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$sidebars = $GLOBALS['wp_registered_sidebars'];

		$options = [];
		foreach ( $sidebars as $sidebar ) {
			$sidebar_name = $sidebar['name'];
			$options[] = [
				'value' => $sidebar['id'],
				'label' => $sidebar_name,
				'selected' => $sidebar['id'] === $value,
			];
		}

		return WPB_Form_Field_Dropdown::get( [
			'name'        => $settings['param_name'],
			'id'         => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
			'classes' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'], '', 'dropdown wpb-input wpb-form-select' ),
			'options' => $options,
		] );
	}
endif;

if ( ! function_exists( 'vc_number_form_field' ) ) :
	/**
	 * Number shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @since 9.0
	 * @return string - html string.
	 */
	function vc_number_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$unit_data = WPB_Unit_Option::parse_value( $value, $settings['settings']['units'] ?? false );
		$value = $unit_data['value'];
		$default_value = $settings['value'] ?? null;
		return WPB_Form_Field_Number::get(
			[
				'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
				'classes' => wpbakery()->editForm()->get_value_control_classes( $settings['param_name'], $settings['type'] ),
				'value' => is_null( $value ) ? $default_value : $value,
				'name' => $settings['param_name'],
				'placeholder' => $settings['settings']['placeholder'] ?? '',
				'min' => $settings['settings']['min'] ?? '',
				'max' => $settings['settings']['max'] ?? '',
				'step' => $settings['settings']['step'] ?? '',
				'units' => $unit_data['units'],
				'selected_unit' => $unit_data['selected_unit'],
			]
		);
	}
endif;

if ( ! function_exists( 'vc_tag_input_form_field' ) ) :
	/**
	 * Tag_input shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @since 9.0
	 * @return string - html string.
	 */
	function vc_tag_input_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		if ( is_string( $value ) ) {
			if ( '[]' === $value ) {
				$value = [];
			} else {
				$value = explode( ',', $value );
			}
		}

		if ( ! is_array( $value ) ) {
			$value = [];
		}

		// we saved empty field.
		if ( is_array( $value ) && 1 === count( $value ) && '' === $value[0] ) {
			$value = [];
		}

		return vc_get_template( 'params/tag_input/template.php', [
			'settings' => $settings,
			'value' => $value,
			'param_id' => $param_id,
		] );
	}
endif;

if ( ! function_exists( 'vc_dropdown_form_field' ) ) :
	/**
	 * Dropdown(select with options) shortcode attribute type generator.
	 *
	 * @param array $settings
	 * @param mixed $value
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string - html string.
	 * @since 4.4
	 */
	function vc_dropdown_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$dropdown = new WpbParamDropdown();
		return $dropdown->render( $settings, $value, $tag, $param_id );
	}
endif;

if ( ! function_exists( 'vc_link_form_field' ) ) :
	/**
	 * Render link parameter field for element edit form.
	 *
	 * @param array  $settings Parameter settings from element config.
	 * @param string $value    Current value (pipe-delimited or plain URL).
	 * @param string $tag
	 * @param string $param_id
	 *
	 * @return string HTML markup for the link field.
	 * @since 9.0
	 */
	function vc_link_form_field( $settings, $value, $tag = '', $param_id = '' ) {
		$link_param = new WpbParamLink();
		return $link_param->render( $settings, $value, $tag, $param_id );
	}
endif;
