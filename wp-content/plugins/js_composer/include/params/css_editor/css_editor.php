<?php
/**
 * Param type 'css_editor'.
 *
 * Used to create dropdown field with animation styles.
 * Usually we use it in 'Design Options' tab of edit element window.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'WPBakeryCssEditor' ) ) {
	/**
	 * Class WPBakeryCssEditor
	 */
	class WPBakeryCssEditor {
		/**
		 * Holds the settings array for the CSS editor.
		 *
		 * @var array
		 */
		protected $settings = [];
		/**
		 * Stores the current value for the CSS editor.
		 *
		 * @var string
		 */
		protected $value = '';

		/**
		 * Contains the positions for the CSS properties.
		 *
		 * @var array
		 */
		protected $positions = [
			'top',
			'right',
			'bottom',
			'left',
		];

		/**
		 * Stores the parameters passed to the CSS editor.
		 *
		 * @var array
		 */
		public $params = [];

		/**
		 * Setters/Getters
		 *
		 * @param null $settings
		 *
		 * @return array
		 */
		public function settings( $settings = null ) {
			if ( is_array( $settings ) ) {
				$this->settings = $settings;
			}

			return $this->settings;
		}

		/**
		 * Retrieves a specific setting by key.
		 *
		 * @param string $key
		 *
		 * @return string
		 */
		public function setting( $key ) {
			return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : '';
		}

		/**
		 * Sets or gets the current value of the CSS editor.
		 *
		 * @param null $value
		 *
		 * @return string
		 */
		public function value( $value = null ) {
			if ( is_string( $value ) ) {
				$this->value = $value;
			}

			return $this->value;
		}

		/**
		 * Sets or gets the parameters array.
		 *
		 * @param null $values
		 *
		 * @return array
		 */
		public function params( $values = null ) {
			if ( is_array( $values ) ) {
				$this->params = $values;
			}

			return $this->params;
		}

		/**
		 * Renders param form field output.
		 *
		 * @return mixed
		 * @see vc_filter: vc_css_editor - hook to override output of this method
		 */
		public function render() {
			$output = vc_get_template( 'params/css_editor/template.php', [
				'css_editor' => $this,
			] );

			return apply_filters( 'vc_css_editor', $output );
		}

		/**
		 * Generates the HTML for the background image control.
		 *
		 * @return string
		 */
		public function get_background_image_control() {
			$add_image_label = esc_html__( 'Add image', 'js_composer' );
			$value = sprintf( '<div class="gallery_widget_attached_image_wrapper"></div><button type="button" class="gallery_widget_add_images vc_add-image" use-single="true" data-is-do="true" title="' . $add_image_label . '" aria-label="' . $add_image_label . '"><i class="vc-composer-icon vc-c-image"></i>%s</button>', $add_image_label );

			return apply_filters( 'vc_css_editor_background_image_control', $value );
		}

		/**
		 * Get border radius options.
		 *
		 * @since 9.0
		 * @return array
		 */
		public function get_border_radius_options() {
			$radius_list = apply_filters( 'vc_css_editor_border_radius_options_data', [
				'' => esc_html__( 'None', 'js_composer' ),
				'1px' => '1px',
				'2px' => '2px',
				'3px' => '3px',
				'4px' => '4px',
				'5px' => '5px',
				'10px' => '10px',
				'15px' => '15px',
				'20px' => '20px',
				'25px' => '25px',
				'30px' => '30px',
				'35px' => '35px',
			] );

			$options = [];
			foreach ( $radius_list as $radius => $title ) {
				$options[] = [
					'value' => $radius,
					'label' => $title,
				];
			}

			return $options;
		}

		/**
		 * Get border style options.
		 *
		 * @since 9.0
		 * @return array
		 */
		public function get_border_style_options() {
			$styles = apply_filters( 'vc_css_editor_border_style_options_data', [
				esc_html__( 'solid', 'js_composer' ),
				esc_html__( 'dotted', 'js_composer' ),
				esc_html__( 'dashed', 'js_composer' ),
				esc_html__( 'none', 'js_composer' ),
				esc_html__( 'hidden', 'js_composer' ),
				esc_html__( 'double', 'js_composer' ),
				esc_html__( 'groove', 'js_composer' ),
				esc_html__( 'ridge', 'js_composer' ),
				esc_html__( 'inset', 'js_composer' ),
				esc_html__( 'outset', 'js_composer' ),
				esc_html__( 'initial', 'js_composer' ),
				esc_html__( 'inherit', 'js_composer' ),
			] );

			$options = [];
			foreach ( $styles as $style ) {
				$options[] = [
					'value' => $style,
					'label' => ucfirst( $style ),
				];
			}

			return $options;
		}

		/**
		 * Get background style options.
		 *
		 * @since 9.0
		 * @return array
		 */
		public function get_background_style_options() {
			$styles = apply_filters( 'vc_css_editor_background_style_options_data', [
				esc_html__( 'Cover', 'js_composer' ) => 'cover',
				esc_html__( 'Contain', 'js_composer' ) => 'contain',
				esc_html__( 'No Repeat', 'js_composer' ) => 'no-repeat',
				esc_html__( 'Repeat', 'js_composer' ) => 'repeat',
			] );

			$default = $this->setting( 'background_style_default' );

			$options = [];
			foreach ( $styles as $name => $style ) {
				$option = [
					'value' => $style,
					'label' => $name,
				];
				if ( $default && $default === $style ) {
					$option['selected'] = true;
				}
				$options[] = $option;
			}

			return $options;
		}

		/**
		 * Generates the HTML options for the border radius dropdown.
		 *
		 * @return string
		 */
		public function getBorderRadiusOptions() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			_deprecated_function(
				__METHOD__,
				'9.0',
				'WPBakeryCssEditor::get_border_radius_options'
			);
			$radiuses = apply_filters( 'vc_css_editor_border_radius_options_data', [
				'' => esc_html__( 'None', 'js_composer' ),
				'1px' => '1px',
				'2px' => '2px',
				'3px' => '3px',
				'4px' => '4px',
				'5px' => '5px',
				'10px' => '10px',
				'15px' => '15px',
				'20px' => '20px',
				'25px' => '25px',
				'30px' => '30px',
				'35px' => '35px',
			] );

			$output = '';
			foreach ( $radiuses as $radius => $title ) {
				$output .= '<option value="' . $radius . '">' . $title . '</option>';
			}

			return $output;
		}

		/**
		 * Generates the HTML options for the border style dropdown.
		 *
		 * @deprecated since 9.0. Use WPBakeryCssEditor::get_border_style_options() instead.
		 * @return string
		 */
		public function getBorderStyleOptions() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			_deprecated_function(
				__METHOD__,
				'9.0',
				'WPBakeryCssEditor::get_border_style_options()'
			);
			$output = '<option value="">' . esc_html__( 'Theme defaults', 'js_composer' ) . '</option>';
			$styles = apply_filters( 'vc_css_editor_border_style_options_data', [
				esc_html__( 'solid', 'js_composer' ),
				esc_html__( 'dotted', 'js_composer' ),
				esc_html__( 'dashed', 'js_composer' ),
				esc_html__( 'none', 'js_composer' ),
				esc_html__( 'hidden', 'js_composer' ),
				esc_html__( 'double', 'js_composer' ),
				esc_html__( 'groove', 'js_composer' ),
				esc_html__( 'ridge', 'js_composer' ),
				esc_html__( 'inset', 'js_composer' ),
				esc_html__( 'outset', 'js_composer' ),
				esc_html__( 'initial', 'js_composer' ),
				esc_html__( 'inherit', 'js_composer' ),
			] );
			foreach ( $styles as $style ) {
				$output .= '<option value="' . $style . '">' . ucfirst( $style ) . '</option>';
			}

			return $output;
		}

		/**
		 * Generates the HTML options for the background style dropdown.
		 *
		 * @return string
		 */
		public function getBackgroundStyleOptions() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			_deprecated_function(
				__METHOD__,
				'9.0',
				'WPBakeryCssEditor::get_background_style_options()'
			);
			$output = '<option value="">' . esc_html__( 'Theme defaults', 'js_composer' ) . '</option>';
			$styles = apply_filters( 'vc_css_editor_background_style_options_data', [
				esc_html__( 'Cover', 'js_composer' ) => 'cover',
				esc_html__( 'Contain', 'js_composer' ) => 'contain',
				esc_html__( 'No Repeat', 'js_composer' ) => 'no-repeat',
				esc_html__( 'Repeat', 'js_composer' ) => 'repeat',
			] );
			foreach ( $styles as $name => $style ) {
				$output .= '<option value="' . $style . '">' . $name . '</option>';
			}

			return $output;
		}

		/**
		 * Returns a unique ID prefix for this css_editor instance, scoped to its param_name.
		 *
		 * Falls back to the legacy prefix when param_name is not set, preserving backward compatibility.
		 *
		 * @return string
		 */
		protected function get_id_prefix() {
			$param_name = $this->setting( 'param_name' );
			if ( $param_name ) {
				return 'vc_css-editor-' . sanitize_html_class( $param_name );
			}
			return 'vc_css-editor';
		}

		/**
		 * Returns a unique element ID for a named field within this instance.
		 *
		 * @param string $field Field identifier (e.g. 'border-style', 'background-style').
		 * @return string
		 */
		public function get_field_id( $field ) {
			return $this->get_id_prefix() . '-' . $field;
		}

		/**
		 * Generates the onion layout structure for the CSS editor.
		 *
		 * @return string
		 */
		public function onionLayout() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			$link_toggle = '<button type="button" class="vc_css-editor-link-toggle" title="' . esc_attr__( 'Link values', 'js_composer' ) . '" aria-label="' . esc_attr__( 'Link values', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-not-linked"></i><i class="vc-composer-icon vc-c-linked"></i></button>';

			$margin_label = $this->layer_label( 'margin' );
			$border_controls = $this->layerControls( 'border', 'width' );
			$padding_controls = $this->layerControls( 'padding' );

			$output = sprintf(
				'<div class="vc_layout-onion"><div class="vc_margin">%s%s%s%s<div class="vc_border">%s%s<div class="vc_padding">%s<div class="vc_content"></div></div></div></div></div>',
				$margin_label,
				$this->get_layer_unit_selector( 'margin' ),
				$link_toggle,
				$this->layer_inputs( 'margin' ),
				$border_controls,
				wpb_border_radius_controls(),
				$padding_controls
			);

			return apply_filters( 'vc_css_editor_onion_layout', $output );
		}

		/**
		 * Generates only the label for a specific layer.
		 *
		 * @param string $name The layer name (margin, border, padding).
		 *
		 * @return string
		 */
		protected function layer_label( $name ) {
			if ( 'margin' === $name ) {
				$label = esc_html__( 'Margin', 'js_composer' );
			} elseif ( 'padding' === $name ) {
				$label = esc_html__( 'Padding', 'js_composer' );
			} elseif ( 'border' === $name ) {
				$label = esc_html__( 'Border and radius', 'js_composer' );
			} else {
				$label = $name;
			}
			$for_id = $this->get_id_prefix() . '-' . esc_attr( $name ) . '-top';

			return '<label class="vc_layout-onion-label" for="' . $for_id . '">' . esc_html( $label ) . '</label>';
		}

		/**
		 * Generates only the input fields for a specific layer (no label).
		 *
		 * @param string $name The layer name (margin, border, padding).
		 * @param string $prefix Optional prefix for the input names.
		 *
		 * @return string
		 */
		protected function layer_inputs( $name, $prefix = '' ) {
			$output = '';
			foreach ( $this->positions as $pos ) {
				$id_attr = 'top' === $pos ? ' id="' . $this->get_id_prefix() . '-' . esc_attr( $name ) . '-top"' : '';
				$output .= sprintf( '<input type="text"%s name="%s_%s%s" data-name="%s%s-%s" class="vc_%s" placeholder="" data-attribute="%s" value="">', $id_attr, esc_attr( $name ), esc_attr( $pos ), '' !== $prefix ? '_' . esc_attr( $prefix ) : '', esc_attr( $name ), '' !== $prefix ? '-' . esc_attr( $prefix ) : '', esc_attr( $pos ), esc_attr( $pos ), esc_attr( $name ) );
			}

			return apply_filters( 'vc_css_editor_layer_inputs', $output );
		}

		/**
		 * Generates the controls for a specific layer (e.g., margin, border).
		 *
		 * @param string $name The layer name.
		 * @param string $prefix Optional prefix for the input names.
		 *
		 * @return string
		 */
		protected function layerControls( $name, $prefix = '' ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
			if ( 'margin' === $name ) {
				$label = esc_html__( 'Margin', 'js_composer' );
			} elseif ( 'padding' === $name ) {
				$label = esc_html__( 'Padding', 'js_composer' );
			} elseif ( 'border' === $name ) {
				$label = esc_html__( 'Border and radius', 'js_composer' );
			} else {
				$label = $name;
			}
			$for_id = $this->get_id_prefix() . '-' . esc_attr( $name ) . '-top';
			$output = '<label for="' . $for_id . '">' . esc_html( $label ) . '</label>';
			$output .= $this->get_layer_unit_selector( $name );
			foreach ( $this->positions as $pos ) {
				$id_attr = 'top' === $pos ? ' id="' . $for_id . '"' : '';
				$output .= sprintf( '<input type="text"%s name="%s_%s%s" data-name="%s%s-%s" class="vc_%s" placeholder="" data-attribute="%s" value="">', $id_attr, esc_attr( $name ), esc_attr( $pos ), '' !== $prefix ? '_' . esc_attr( $prefix ) : '', esc_attr( $name ), '' !== $prefix ? '-' . esc_attr( $prefix ) : '', esc_attr( $pos ), esc_attr( $pos ), esc_attr( $name ) );
			}

			return apply_filters( 'vc_css_editor_layer_controls', $output );
		}

		/**
		 * Generates a unit selector dropdown for a layer.
		 *
		 * @param string $name The layer name (margin, border, padding).
		 *
		 * @return string
		 * @since 9.0
		 */
		protected function get_layer_unit_selector( $name ) {
			$default_units = 'border' === $name ? [ 'px', 'em', 'rem' ] : [ 'px', '%', 'em', 'rem', 'vw', 'vh' ];
			$units = apply_filters( 'vc_css_editor_units', $default_units, $name );

			return vc_get_template( 'editors/partials/param-unit-selector.tpl.php', [
				'units'           => $units,
				'selected_unit'   => 'px',
				'data_attributes' => [ 'layer' => $name ],
			] );
		}
	}
}

if ( ! function_exists( 'wpb_border_radius_controls' ) ) :
	/**
	 * Generates the four border-radius corner input controls for the CSS editor onion layout.
	 *
	 * @return string HTML markup for the four border-radius corner inputs.
	 * @since 8.6
	 */
	function wpb_border_radius_controls() {
		$corners = [
			'top_left'     => 'border-top-left-radius',
			'top_right'    => 'border-top-right-radius',
			'bottom_right' => 'border-bottom-right-radius',
			'bottom_left'  => 'border-bottom-left-radius',
		];

		$output = '<div class="vc_border-radius-corners">';
		foreach ( $corners as $corner => $property ) {
			$output .= sprintf(
				'<input type="text" name="%s" data-name="%s" class="vc_corner_%s" placeholder="" data-attribute="border-radius" value="">',
				esc_attr( $property ),
				esc_attr( $property ),
				esc_attr( $corner )
			);
		}
		$output .= '</div>';

		return $output;
	}
endif;

/**
 * Renders the CSS editor param form field.
 *
 * @param array $settings
 * @param string $value
 *
 * @return mixed
 */
function vc_css_editor_form_field( $settings, $value ) {
	$css_editor = new WPBakeryCssEditor();
	$css_editor->settings( $settings );
	$css_editor->value( $value );

	return $css_editor->render();
}
