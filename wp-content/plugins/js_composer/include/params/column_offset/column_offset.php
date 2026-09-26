<?php
/**
 * Param type "column_offset".
 *
 * Used to create dropdown for width responsiveness
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Vc_Column_Offset class.
 */
class Vc_Column_Offset {
	/**
	 * The settings for the column offset, passed to the constructor.
	 *
	 * @var array
	 */
	protected $settings = [];

	/**
	 * The value associated with the column offset.
	 *
	 * @var string
	 */
	protected $value = '';

	/**
	 * The available size types for the column offset.
	 *
	 * @var array
	 */
	protected $size_types = [];

	/**
	 * A list of possible column widths.
	 *
	 * @var array
	 */
	protected $column_width_list = [];

	/**
	 * Parsed data from the $value attribute.
	 *
	 * @var array|mixed
	 */
	protected $data = [];

	/**
	 * Vc_Column_Offset constructor.
	 *
	 * @param array $settings
	 * @param string $value
	 */
	public function __construct( $settings, $value ) {
		$this->settings = $settings;
		$this->value = $value;

		$this->size_types = [
			'xl' => esc_html__( 'Desktop', 'js_composer' ),
			'lg' => esc_html__( 'Tablet landscape', 'js_composer' ),
			'md' => esc_html__( 'Tablet portrait', 'js_composer' ),
			'sm' => esc_html__( 'Mobile landscape', 'js_composer' ),
			'xs' => esc_html__( 'Mobile portrait', 'js_composer' ),
		];

		$this->column_width_list = [
			esc_html__( '1/12 - 8.33%', 'js_composer' ) => '1',
			esc_html__( '1/6 - 16.67%', 'js_composer' ) => '2',
			esc_html__( '1/4 - 25%', 'js_composer' ) => '3',
			esc_html__( '1/3 - 33.33%', 'js_composer' ) => '4',
			esc_html__( '5/12 - 41.67%', 'js_composer' ) => '5',
			esc_html__( '1/2 - 50%', 'js_composer' ) => '6',
			esc_html__( '7/12 - 58.33%', 'js_composer' ) => '7',
			esc_html__( '2/3 - 66.66%', 'js_composer' ) => '8',
			esc_html__( '3/4 - 75%', 'js_composer' ) => '9',
			esc_html__( '5/6 - 83.33%', 'js_composer' ) => '10',
			esc_html__( '11/12 - 91.67%', 'js_composer' ) => '11',
			esc_html__( '1/1 - 100%', 'js_composer' ) => '12',
			esc_html__( '1/5 - 20%', 'js_composer' ) => '1/5',
			esc_html__( '2/5 - 40%', 'js_composer' ) => '2/5',
			esc_html__( '3/5 - 60%', 'js_composer' ) => '3/5',
			esc_html__( '4/5 - 80%', 'js_composer' ) => '4/5',
		];
	}

	/**
	 * Render the column offset param.
	 *
	 * @return string
	 */
	public function render() {
		ob_start();
		vc_include_template( 'params/column_offset/template.tpl.php', [
			'settings' => $this->settings,
			'value' => $this->value,
			'data' => $this->valueData(),
			'sizes' => $this->size_types,
			'param' => $this,
		] );

		return ob_get_clean();
	}

	/**
	 * Parses and returns the data associated with the value.
	 *
	 * @return array|mixed
	 */
	public function valueData() {
		if ( empty( $this->data ) ) {
			$this->data = ! empty( $this->value ) ? preg_split( '/\s+/', wpb_normalize_column_offset_hidden( $this->value ) ) : [];
		}

		return $this->data;
	}

	/**
	 * Generates the HTML select element for size control.
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public function sizeControl( $size ) {
		$empty_label = 'xs' === $size ? esc_html__( 'Default', 'js_composer' ) : esc_html__( 'Inherit', 'js_composer' );
		$options = [];
		$options[] = [
			'label' => $empty_label,
			'value' => '',
		];

		// For non-xs viewports, check if user explicitly set "Inherit".
		$has_explicit_inherit = in_array( 'vc_col-' . $size . '-inherit', $this->data, true );

		// For md viewport, fall back to width param for backward compatibility.
		// Only if no md value exists in offset AND no explicit inherit marker.
		$selected_bc_index = null;
		if ( 'md' === $size && ! $has_explicit_inherit ) {
			$has_size_in_offset = false;
			foreach ( $this->column_width_list as $index ) {
				if ( in_array( 'vc_col-md-' . $index, $this->data, true ) ) {
					$has_size_in_offset = true;
					break;
				}
			}
			// Only use width param if no md value exists in offset data.
			if ( ! $has_size_in_offset && isset( $this->settings['width'] ) ) {
				$selected_bc_index = $this->getColumnIndexFromWidth( $this->settings['width'] );
			}
		}

		foreach ( $this->column_width_list as $label => $index ) {
			$value = 'vc_col-' . $size . '-' . $index;
			$is_selected = in_array( $value, $this->data, true );

			// For md viewport BC: use width param value if no md in offset.
			if ( 'md' === $size && null !== $selected_bc_index && $index === $selected_bc_index ) {
				$is_selected = true;
			}

			$options[] = [
				'label' => $label,
				'value' => $value,
				'selected' => $is_selected,
			];
		}

		return WPB_Form_Field_Dropdown::get( [
			'id' => "vc_col_{$size}_size",
			'name' => "vc_col_{$size}_size",
			'classes' => 'vc_column_offset_field',
			'options' => $options,
			'data_attributes' => [
				'type' => "size-{$size}",
			],
		] );
	}

	/**
	 * Convert width param value (e.g., '1/2') to column index (e.g., '6').
	 *
	 * @param string $width
	 * @return string|null
	 */
	protected function getColumnIndexFromWidth( $width ) {
		$width_to_index = [
			'1/12' => '1',
			'1/6' => '2',
			'1/4' => '3',
			'1/3' => '4',
			'5/12' => '5',
			'1/2' => '6',
			'7/12' => '7',
			'2/3' => '8',
			'3/4' => '9',
			'5/6' => '10',
			'11/12' => '11',
			'1/1' => '12',
			'1/5' => '1/5',
			'2/5' => '2/5',
			'3/5' => '3/5',
			'4/5' => '4/5',
		];

		return isset( $width_to_index[ $width ] ) ? $width_to_index[ $width ] : null;
	}


	/**
	 * Generates the HTML select element for offset control.
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public function offsetControl( $size ) {
		$prefix = 'vc_col-' . $size . '-offset-';
		$empty_label = 'xs' === $size ? esc_html__( 'No offset', 'js_composer' ) : esc_html__( 'Inherit', 'js_composer' );

		$options[] = [
			'label' => $empty_label,
			'value' => '',
		];
		if ( 'xs' !== $size ) {
			$options[] = [
				'label' => esc_html__( 'No offset', 'js_composer' ),
				'value' => $prefix . '0',
				'selected' => in_array( $prefix . '0', $this->data, true ),
			];
		}
		foreach ( $this->column_width_list as $label => $index ) {
			$value = $prefix . $index;
			$options[] = [
				'label' => $label,
				'value' => $value,
				'selected' => in_array( $value, $this->data, true ),
			];
		}

		return WPB_Form_Field_Dropdown::get( [
			'id' => "vc_col_{$size}_offset_size",
			'name' => "vc_col_{$size}_offset_size",
			'classes' => 'vc_column_offset_field',
			'options' => $options,
			'data_attributes' => [
				'type' => "offset-{$size}",
			],
		] );
	}
}

/**
 * Renders the form field for column offset settings.
 *
 * @param array $settings
 * @param string $value
 *
 * @return string
 */
function vc_column_offset_form_field( $settings, $value ) {
	$column_offset = new Vc_Column_Offset( $settings, $value );

	return $column_offset->render();
}

if ( ! function_exists( 'wpb_normalize_column_offset_hidden' ) ) :
	/**
	 * Add the xl hide class to pre-9.0 offset values, where vc_hidden-lg meant hidden on all desktop screens.
	 *
	 * @param string $offset Column offset attribute value.
	 * @return string
	 * @since 9.0.1
	 */
	function wpb_normalize_column_offset_hidden( $offset ) {
		$is_legacy_hidden = false !== strpos( $offset, 'vc_hidden-lg' ) && false === strpos( $offset, 'vc_col-xl-' ) && false === strpos( $offset, 'vc_hidden-xl' );

		return $is_legacy_hidden ? $offset . ' vc_hidden-xl' : $offset;
	}
endif;

/**
 * Merges the column offset class with the column width class.
 *
 * @param string $column_offset
 * @param string $width
 *
 * @return string
 */
function vc_column_offset_class_merge( $column_offset, $width ) {
	$column_offset = wpb_normalize_column_offset_hidden( $column_offset );

	// Remove offset settings if responsive CSS is disabled.
	if ( '1' === vc_settings()->get( 'not_responsive_css' ) ) {
		$column_offset = preg_replace( '/vc_col-(lg|md|xs)[^\s]*/', '', $column_offset );
	}

	// Check if user explicitly set any viewport to "Inherit" (indicated by inherit markers).
	// If so, remove the markers and don't add the default sm class from width param.
	$has_inherit_marker = preg_match( '/vc_col-(xl|lg|md|sm)-inherit/', $column_offset );

	if ( $has_inherit_marker ) {
		// Remove inherit markers, preserving other classes.
		$column_offset = preg_replace( '/vc_col-(xl|lg|md|sm)-inherit/', '', $column_offset );
		// Clean up multiple spaces and trim.
		return trim( preg_replace( '/\s+/', ' ', $column_offset ) );
	}

	if ( preg_match( '/vc_col-sm-\d+/', $column_offset ) ) {
		return $column_offset;
	}

	return $width . ( empty( $column_offset ) ? '' : ' ' . $column_offset );
}

/**
 * Registers the column offset parameter with Visual Composer.
 */
function vc_load_column_offset_param() {
	vc_add_shortcode_param( 'column_offset', 'vc_column_offset_form_field' );
}

add_action( 'vc_load_default_params', 'vc_load_column_offset_param' );

/**
 * Pass the width attribute to the column_offset param settings.
 *
 * This filter injects the 'width' shortcode attribute into the param settings
 * so it can be accessed by the Vc_Column_Offset class for backward compatibility.
 *
 * @param array $param The param settings.
 * @param mixed $value The param value.
 * @param array $settings The shortcode settings.
 * @param array $atts The shortcode attributes.
 *
 * @return array
 */
function vc_column_offset_inject_width_param( $param, $value, $settings, $atts ) {
	$param['width'] = isset( $atts['width'] ) ? $atts['width'] : '1/1';

	return $param;
}

add_filter( 'vc_form_fields_render_field_vc_column_offset_param', 'vc_column_offset_inject_width_param', 10, 4 );
add_filter( 'vc_form_fields_render_field_vc_column_inner_offset_param', 'vc_column_offset_inject_width_param', 10, 4 );
