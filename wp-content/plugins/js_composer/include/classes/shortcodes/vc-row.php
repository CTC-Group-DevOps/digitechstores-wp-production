<?php
/**
 * Class that handles specific [vc_row] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_row.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Page Builder row
 *
 * @package WPBakeryPageBuilder
 */
class WPBakeryShortCode_Vc_Row extends WPBakeryShortCode {
	/**
	 * Predefined attributes for shortcode.
	 *
	 * @var array
	 */
	protected $predefined_atts = [
		'el_class' => '',
	];

	/**
	 * Non draggable class.
	 *
	 * @var string
	 */
	public $nonDraggableClass = 'vc-non-draggable-row';

	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	public function __construct( $settings ) {
		parent::__construct( $settings );
		$this->shortcodeScripts();
	}

	/**
	 * Register shortcode scripts.
	 */
	protected function shortcodeScripts() {
		wp_register_script( 'vc_jquery_skrollr_js', vc_asset_url( 'lib/vendor/dist/skrollr/dist/skrollr.min.js' ), [ 'jquery-core' ], WPB_VC_VERSION, true );
		wp_register_script( 'vc_youtube_iframe_api_js', 'https://www.youtube.com/iframe_api', [], WPB_VC_VERSION, true );
	}

	/**
	 * Get shortcode output.
	 *
	 * @param array $atts
	 * @param null $content
	 * @return mixed|string
	 */
	protected function content( $atts, $content = null ) {
		$prefix = '';

		return $prefix . $this->loadTemplate( $atts, $content ); // nosemgrep - escaping handled inside templates.
	}

	/**
	 * This returns block controls.
	 */
	public function getLayoutsControl() {
		global $vc_row_layouts;
		$controls_layout = '<span class="vc_row_layouts vc_control">';
		foreach ( $vc_row_layouts as $layout ) {
			$controls_layout .= '<a class="vc_control-set-column set_columns" data-cells="' . $layout['cells'] . '" data-cells-mask="' . $layout['mask'] . '" title="' . $layout['title'] . '"><i class="vc-composer-icon vc-c-icon-' . $layout['icon_class'] . '"></i></a> ';
		}
		$controls_layout .= '<br/><a class="vc_control-set-column set_columns custom_columns" data-cells="custom" data-cells-mask="custom" title="' . esc_attr__( 'Custom layout', 'js_composer' ) . '">' . esc_html__( 'Custom', 'js_composer' ) . '</a> ';
		$controls_layout .= '</span>';

		return $controls_layout;
	}

	/**
	 * Get column control settings.
	 *
	 * @since 9.0
	 *
	 * @param string $extended_css
	 * @return array
	 */
	public function get_column_control_settings( $extended_css = '' ) {
		$settings = [
			'add' => [
				'classes' => 'column_add vc_column-add',
				'title' => __( 'Add column', 'js_composer' ),
				'icon' => 'vc-c-add-circle',
			],
			'delete' => [
				'classes' => 'column_delete vc_column-delete',
				'title' => __( 'Delete this row', 'js_composer' ),
				'icon' => 'vc-c-trash',
			],
			'clone' => [
				'classes' => 'column_clone vc_column-clone',
				'title' => __( 'Clone this row', 'js_composer' ),
				'icon' => 'vc-c-icon-clone',
			],
			'copy' => [
				'classes' => 'column_copy vc_column-copy',
				'title' => __( 'Copy this row', 'js_composer' ),
				'icon' => 'vc-c-icon-copy',
			],
			'paste' => [
				'classes' => 'column_paste vc_column-paste',
				'title' => __( 'Paste', 'js_composer' ),
				'icon' => 'vc-c-icon-paste',
			],
			'edit' => [
				'classes' => 'column_edit vc_column-edit',
				'title' => __( 'Edit this row', 'js_composer' ),
				'icon' => 'vc-c-edit',
			],
			'toggle' => [
				'classes' => 'column_toggle vc_column-toggle',
				'title' => __( 'Toggle row', 'js_composer' ),
				'icon' => 'vc-c-icon-arrow_drop_down',
			],
		];

		$move_access = vc_user_access()->part( 'dragndrop' )->checkStateAny( true, null )->get();
		if ( $move_access ) {
			$settings['move'] = [
				'classes' => 'column_move vc_column-move',
				'title' => __( 'Drag row to reorder', 'js_composer' ),
				'icon' => 'vc-c-param-group-dragndrop',
			];
		}

		return $settings;
	}

	/**
	 * Get column controls.
	 *
	 * @param mixed $controls
	 * @param string $extended_css
	 * @return string
	 * @throws \Exception
	 */
	public function getColumnControls( $controls, $extended_css = '' ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh, CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		$output = '<div class="vc_controls vc_controls-row controls_row vc_clearfix">';
		$controls_end = '</div>';
		// Create columns.
		$controls_layout = $this->getLayoutsControl();

		$control_list = $this->get_column_controls_html_list();
		$controls_move = isset( $control_list['move'] ) ? $control_list['move'] : '';

		$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
		$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );

		if ( is_array( $controls ) && ! empty( $controls ) ) {
			foreach ( $controls as $control ) {
				$control_var = 'controls_' . $control;
				if ( ( $editAccess && 'edit' === $control ) || $allAccess ) {
					if ( isset( ${$control_var} ) ) {
						$output .= ${$control_var};
					}
				}
			}
			$output .= $controls_end;
		} elseif ( is_string( $controls ) ) {
			$control_var = 'controls_' . $controls;
			if ( ( $editAccess && 'edit' === $controls ) || $allAccess ) {
				if ( isset( ${$control_var} ) ) {
					$output .= ${$control_var} . $controls_end;
				}
			}
		} else {
			$row_edit_clone_delete = '<span class="vc_row_edit_clone_delete">';
			if ( $allAccess ) {
				$copypaste = vc_get_template( 'editors/partials/backend_copypaste_control.tpl.php' );
				$row_edit_clone_delete .= $control_list['delete'] . $copypaste . $control_list['clone'] . $control_list['edit'];
			} elseif ( $editAccess ) {
				$row_edit_clone_delete .= $control_list['edit'];
			}
			$row_edit_clone_delete .= $control_list['toggle'];
			$row_edit_clone_delete .= '</span>';

			if ( $allAccess ) {
				$output .= '<div>' . $controls_move . $controls_layout . $control_list['add'] . '</div>' . $row_edit_clone_delete . $controls_end;
			} elseif ( $editAccess ) {
				$output .= $row_edit_clone_delete . $controls_end;
			} else {
				$output .= $row_edit_clone_delete . $controls_end;
			}
		}

		return $output;
	}

	/**
	 * Load template.
	 *
	 * @param array $atts
	 * @param null $content
	 * @return string
	 * @throws \Exception
	 */
	public function contentAdmin( $atts, $content = null ) {
		$atts = shortcode_atts( $this->predefined_atts, $atts );

		$output = '';

		$column_controls = $this->getColumnControls( $this->settings( 'controls' ) );

		$output .= '<div data-element_type="' . $this->settings['base'] . '" class="' . $this->cssAdminClass() . '">';
		$output .= str_replace( '%column_size%', 1, $column_controls );
		$output .= '<div class="wpb_element_wrapper">';
		$output .= '<div class="vc_row vc_row-fluid wpb_row_container vc_container_for_children">';
		if ( '' === $content && ! empty( $this->settings['default_content_in_template'] ) ) {
			$output .= do_shortcode( shortcode_unautop( $this->settings['default_content_in_template'] ) );
		} else {
			$output .= do_shortcode( shortcode_unautop( $content ) );

		}
		$output .= '</div>';
		if ( isset( $this->settings['params'] ) ) {
			$inner = '';
			foreach ( $this->settings['params'] as $param ) {
				if ( ! isset( $param['param_name'] ) ) {
					continue;
				}
				$param_value = isset( $atts[ $param['param_name'] ] ) ? $atts[ $param['param_name'] ] : '';
				if ( is_array( $param_value ) ) {
					// Get first element from the array.
					reset( $param_value );
					$first_key = key( $param_value );
					$param_value = $param_value[ $first_key ];
				}
				$inner .= $this->singleParamHtmlHolder( $param, $param_value );
			}
			$output .= $inner;
		}
		$output .= '</div>';
		$output .= '</div>';

		return $output; // nosemgrep - we already escaped everything on this step.
	}

	/**
	 * Add admin class to css.
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function cssAdminClass() {
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? ' wpb_sortable' : ' ' . $this->nonDraggableClass );

		return 'wpb_' . $this->settings['base'] . $sortable . '' . ( ! empty( $this->settings['class'] ) ? ' ' . $this->settings['class'] : '' );
	}

	/**
	 * Custom admin block params.
	 *
	 * @return string
	 * @deprecated 4.5 - due to it is not used anywhere? 4.5
	 * @typo Bock - Block
	 */
	public function customAdminBockParams() {
		// this function is deprecated.

		return '';
	}

	/**
	 * Build additional styles.
	 *
	 * @param string $bg_image
	 * @param string $bg_color
	 * @param string $bg_image_repeat
	 * @param string $font_color
	 * @param string $padding
	 * @param string $margin_bottom
	 *
	 * @return string
	 * @deprecated 4.5
	 */
	public function buildStyle( $bg_image = '', $bg_color = '', $bg_image_repeat = '', $font_color = '', $padding = '', $margin_bottom = '' ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh, CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		// this function is deprecated.

		$has_image = false;
		$style = '';
		$image_url = wp_get_attachment_url( $bg_image );
		if ( $image_url ) {
			$has_image = true;
			$style .= 'background-image: url(' . $image_url . ');';
		}
		if ( ! empty( $bg_color ) ) {
			$style .= vc_get_css_color( 'background-color', $bg_color );
		}
		if ( ! empty( $bg_image_repeat ) && $has_image ) {
			if ( 'cover' === $bg_image_repeat ) {
				$style .= 'background-repeat:no-repeat;background-size: cover;';
			} elseif ( 'contain' === $bg_image_repeat ) {
				$style .= 'background-repeat:no-repeat;background-size: contain;';
			} elseif ( 'no-repeat' === $bg_image_repeat ) {
				$style .= 'background-repeat: no-repeat;';
			}
		}
		if ( ! empty( $font_color ) ) {
			$style .= vc_get_css_color( 'color', $font_color );
		}
		if ( '' !== $padding ) {
			$style .= 'padding: ' . ( preg_match( '/(px|em|\%|pt|cm)$/', $padding ) ? $padding : $padding . 'px' ) . ';';
		}
		if ( '' !== $margin_bottom ) {
			$style .= 'margin-bottom: ' . ( preg_match( '/(px|em|\%|pt|cm)$/', $margin_bottom ) ? $margin_bottom : $margin_bottom . 'px' ) . ';';
		}

		return empty( $style ) ? '' : ' style="' . esc_attr( $style ) . '"';
	}

	/**
	 * Get CSS file names for vc_row shortcode.
	 *
	 * @since 9.0
	 * @return array
	 */
	public function get_shortcode_css_files() {
		return [ 'vc_row', 'content_block', 'rows_columns', 'frontend_vc_row' ];
	}
}
