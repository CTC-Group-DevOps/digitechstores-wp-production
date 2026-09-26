<?php
/**
 * Param type 'font_container'
 *
 * Container param for a set of fields.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Font_Container
 *
 * @since 4.3
 * vc_map examples:
 *  array(
 *        'type' => 'font_container',
 *        'param_name' => 'font_container',
 *        'value'=>'',
 *        'settings'=>array(
 *            'fields'=>array(
 *                'tag'=>'h2',
 *                'text_align',
 *                'font_size',
 *                'line_height',
 *                'color',
 *
 *                'tag_description' => esc_html__('Select element tag.','js_composer'),
 *                'text_align_description' => esc_html__('Select text alignment.','js_composer'),
 *                'font_size_description' => esc_html__('Enter font size.','js_composer'),
 *                'line_height_description' => esc_html__('Enter line height.','js_composer'),
 *                'color_description' => esc_html__('Select color for your element.','js_composer'),
 *            ),
 *        ),
 *    ),
 *  Ordering of fields, font_family, tag, text_align and etc. will be Same as ordering in array!
 *  To provide default value to field use 'key' => 'value'
 */
class Vc_Font_Container {

	/**
	 * Renders the HTML output.
	 *
	 * @param array $settings
	 * @param string $value
	 * @param string $param_id since 9.0.
	 *
	 * @return string
	 */
	public function render( $settings, $value, $param_id = '' ) {
		$fields = [];
		$values = [];
		$initial = $settings['settings']['fields'] ?? [];
		extract( $this->_vc_font_container_parse_attributes( $initial, $value ) );

		$output = vc_get_template( 'params/font_container/input.php', [
			'settings' => $settings,
			'value'    => $value,
		] );

		if ( ! $fields ) {
			return $output; // nosemgrep - we already escaped everything on this step.
		}

		$data = [];
		$templates = $this->get_templates_map( $fields, $values, $settings, $param_id );

		foreach ( $templates as $key => $config ) {
			if ( isset( $fields[ $key ] ) ) {
				$data[ $key ] = vc_get_template(
					$config['template'],
					$config['args']
				);
			}
		}

		$data = apply_filters( 'vc_font_container_output_data', $data, $fields, $values, $settings );

		foreach ( $fields as $key => $field ) {
			if ( isset( $data[ $key ] ) ) {
				$output .= $data[ $key ];
			}
		}

		return $output; // nosemgrep - we already escaped everything on this step.
	}

	/**
	 * Get map of templates for fields.
	 *
	 * @param array $fields
	 * @param array $values
	 * @param array $settings
	 * @param string $param_id
	 * @since 9.0
	 * @return array
	 */
	public function get_templates_map( $fields, $values, $settings, $param_id ) {
		return [
			'tag' => [
				'template' => 'params/font_container/element_tag.php',
				'args' => [
					'fields' => $fields,
					'param_id' => $param_id,
					'settings' => $settings,
					'options' => $this->get_tag_options( $values ),
					'edit_field_class' => $fields['tag_edit_field_class'] ?? '',
				],
			],
			'font_size' => [
				'template' => 'params/font_container/font_size.php',
				'args' => [
					'fields' => $fields,
					'param_id' => $param_id,
					'settings' => $settings,
					'values' => $values,
					'edit_field_class' => $fields['font_size_edit_field_class'] ?? '',
				],
			],
			'text_align' => [
				'template' => 'params/font_container/text_align.php',
				'args' => [
					'value' => $values['text_align'],
					'param_id' => $param_id,
					'settings' => $settings,
					'fields' => $fields,
					'edit_field_class' => $fields['text_align_edit_field_class'] ?? '',
				],
			],
			'line_height' => [
				'template' => 'params/font_container/line_height.php',
				'args' => [
					'fields' => $fields,
					'param_id' => $param_id,
					'settings' => $settings,
					'values' => $values,
					'edit_field_class' => $fields['line_height_edit_field_class'] ?? '',
				],
			],
			'color' => [
				'template' => 'params/font_container/color.php',
				'args' => [
					'fields' => $fields,
					'values' => $values,
					'param_id' => $param_id,
					'settings' => $settings,
					'edit_field_class' => $fields['color_edit_field_class'] ?? '',
				],
			],
			'font_family' => [
				'template' => 'params/font_container/font_family.php',
				'args' => [
					'fields' => $fields,
					'param_id' => $param_id,
					'settings' => $settings,
					'container_class' => 'vc_font_container_form_field-font_family-container',
					'dropdown_atts' => [
						'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'], 'font_family' ),
						'classes' => 'vc_font_container_form_field-font_family-select',
						'options' => $this->get_font_family_options(
							$this->_vc_font_container_get_web_safe_fonts(),
							$values
						),
					],
					'edit_field_class' => $fields['font_family_edit_field_class'] ?? '',
				],
			],
			'font_style' => [
				'template' => 'params/font_container/font_style.php',
				'args' => [
					'fields' => $fields,
					'param_id' => $param_id,
					'values' => $values,
					'edit_field_class' => $fields['font_style_edit_field_class'] ?? '',
				],
			],
		];
	}

	/**
	 * Get options for field 'font_family'
	 *
	 * @since 9.0
	 * @param array $fonts
	 * @param array $values
	 * @return array
	 */
	public function get_font_family_options( $fonts, $values ) {
		$options = [];
		foreach ( $fonts as $font_name => $font_data ) {
			$options[] = [
				'value' => $font_name,
				'label' => $font_name,
				'data_attributes' => [
					'font-family' => rawurlencode( $font_data ),
				],
				'class' => vc_build_safe_css_class( $font_name ),
				'selected' => strtolower( $values['font_family'] ) === strtolower( $font_name ),
			];
		}

		return $options;
	}

	/**
	 * Get options for field 'tag'
	 *
	 * @param array $values
	 * @since 9.0
	 * @return array
	 */
	public function get_tag_options( $values ) {
		$options = [];
		foreach ( $this->_vc_font_container_get_allowed_tags() as $tag ) {
			$options[] = [
				'value' => $tag,
				'label' => $tag,
				'selected' => isset( $values['tag'] ) && $values['tag'] === $tag,
			];

		}

		return $options;
	}

	/**
	 * If field 'font_family' is used this is list of fonts available
	 * To modify this list, you should use add_filter('vc_font_container_get_fonts_filter','your_custom_function');
	 *
	 * @see vc_filter: vc_font_container_get_fonts_filter - to modify list of fonts
	 * @return array list of fonts
	 */
	public function _vc_font_container_get_web_safe_fonts() { // phpcs:ignore:PSR2.Methods.MethodDeclaration.Underscore
		// this is "Web Safe FONTS" from w3c: http://www.w3schools.com/cssref/css_websafe_fonts.asp.
		$web_fonts = [
			'Georgia' => 'Georgia, serif',
			'Palatino Linotype' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
			'Book Antiqua' => '"Book Antiqua", Palatino, serif',
			'Palatino' => 'Palatino, serif',
			'Times New Roman' => '"Times New Roman", Times, serif',
			'Arial' => 'Arial, Helvetica, sans-serif',
			'Arial Black' => '"Arial Black", Gadget, sans-serif',
			'Helvetica' => 'Helvetica, sans-serif',
			'Comic Sans MS' => '"Comic Sans MS", cursive, sans-serif',
			'Impact' => 'Impact, Charcoal, sans-serif',
			'Charcoal' => 'Charcoal, sans-serif',
			'Lucida Sans Unicode' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
			'Lucida Grande' => '"Lucida Grande", sans-serif',
			'Tahoma' => 'Tahoma, Geneva, sans-serif',
			'Geneva' => 'Geneva, sans-serif',
			'Trebuchet MS' => '"Trebuchet MS", Helvetica, sans-serif',
			'Verdana' => '"Trebuchet MS", Helvetica, sans-serif',
			'Courier New' => '"Courier New", Courier, monospace',
			'Lucida Console' => '"Lucida Console", Monaco, monospace',
			'Monaco' => 'Monaco, monospace',
		];

		return apply_filters( 'vc_font_container_get_fonts_filter', $web_fonts );
	}

	/**
	 * If 'tag' field used this is list of allowed tags
	 * To modify this list, you should use add_filter('vc_font_container_get_allowed_tags','your_custom_function');
	 *
	 * @see vc_filter: vc_font_container_get_allowed_tags - to modify list of allowed tags by default
	 * @return array list of allowed tags
	 */
	public function _vc_font_container_get_allowed_tags() { // phpcs:ignore:PSR2.Methods.MethodDeclaration.Underscore
		$allowed_tags = [
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'div',
		];

		return apply_filters( 'vc_font_container_get_allowed_tags', $allowed_tags );
	}

	/**
	 * Parse font container attributes.
	 *
	 * @param array $attr
	 * @param array|string $value
	 *
	 * @return array
	 */
	public function _vc_font_container_parse_attributes( $attr, $value ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore, Generic.Metrics.CyclomaticComplexity.TooHigh
		$fields = [];
		if ( $attr ) {
			foreach ( $attr as $key => $val ) {
				if ( is_numeric( $key ) ) {
					$fields[ $val ] = '';
				} else {
					$fields[ $key ] = $val;
				}
			}
		}

		$values = vc_parse_multi_attribute( $value, [
			'tag' => $fields['tag'] ?? 'h2',
			'font_size' => $fields['font_size'] ?? '',
			'font_style_italic' => $fields['font_style_italic'] ?? '',
			'font_style_bold' => $fields['font_style_bold'] ?? '',
			'font_family' => $fields['font_family'] ?? '',
			'color' => $fields['color'] ?? '',
			'line_height' => $fields['line_height'] ?? '',
			'text_align' => $fields['text_align'] ?? 'left',
			'tag_description' => $fields['tag_description'] ?? '',
			'font_size_description' => $fields['font_size_description'] ?? '',
			'font_style_description' => $fields['font_style_description'] ?? '',
			'font_family_description' => $fields['font_family_description'] ?? '',
			'color_description' => $fields['color_description'] ?? '',
			'line_height_description' => $fields['line_height_description'] ?? '',
			'text_align_description' => $fields['text_align_description'] ?? '',
		] );

		return [
			'fields' => $fields,
			'values' => $values,
		];
	}
}

/**
 * Filter font container render.
 *
 * @param array $settings
 * @param string $value
 * @param string $tag
 * @param string $param_id
 *
 * @return mixed
 */
function vc_font_container_form_field( $settings, $value, $tag, $param_id ) {
	$font_container = new Vc_Font_Container();

	return apply_filters( 'vc_font_container_render_filter', $font_container->render( $settings, $value, $param_id ) );
}
