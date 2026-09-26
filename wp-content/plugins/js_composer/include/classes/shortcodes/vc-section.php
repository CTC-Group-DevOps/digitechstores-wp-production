<?php
/**
 * Class that handles specific [vc_section] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_section.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Page Builder section
 *
 * @package WPBakeryPageBuilder
 */
class WPBakeryShortCode_Vc_Section extends WPBakeryShortCodesContainer {
	/**
	 * Add container classes.
	 *
	 * @param string $width
	 * @param int $i
	 * @return string
	 */
	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="vc_section_container vc_container_for_children"';
	}

	/**
	 * WPBakeryShortCode_Vc_Section constructor.
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
	 * Additional CSS class for shortcode in admin.
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function cssAdminClass() {
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? ' wpb_sortable' : ' ' . $this->nonDraggableClass );

		return 'wpb_' . $this->settings['base'] . $sortable . '' . ( ! empty( $this->settings['class'] ) ? ' ' . $this->settings['class'] : '' );
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
		$add_icon = 'vc-c-add-circle';
		$settings = [
			'add' => [
				'classes' => 'column_add vc_column-add',
				'title'   => esc_attr__( 'Add column', 'js_composer' ),
				'icon'    => $add_icon,
			],
			'delete' => [
				'classes' => 'column_delete vc_column-delete',
				'title'   => esc_attr__( 'Delete this row', 'js_composer' ),
				'icon'    => 'vc-c-trash',
			],
			'edit' => [
				'classes' => 'column_edit vc_column-edit',
				'title'   => esc_attr__( 'Edit this row', 'js_composer' ),
				'icon'    => 'vc-c-edit',
			],
			'clone' => [
				'classes' => 'column_clone vc_column-clone',
				'title'   => esc_attr__( 'Clone this row', 'js_composer' ),
				'icon'    => 'vc-c-icon-clone',
			],
			'copy' => [
				'classes' => 'column_copy vc_column-copy',
				'title'   => esc_attr__( 'Copy this row', 'js_composer' ),
				'icon'    => 'vc-c-icon-copy',
			],
			'paste' => [
				'classes' => 'column_paste vc_column-paste',
				'title'   => esc_attr__( 'Paste', 'js_composer' ),
				'icon'    => 'vc-c-icon-paste',
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
	 * Add column controls to shortcode output.
	 *
	 * @param string $controls
	 * @param string $extended_css
	 * @return string
	 * @throws \Exception
	 */
	public function getColumnControls( $controls = 'full', $extended_css = '' ) {
		$controls_start = '<div class="vc_controls vc_controls-visible controls_column' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';

		$output = '<div class="vc_controls vc_controls-row controls_row vc_clearfix">';
		$controls_end = '</div>';
		$control_list = $this->get_column_controls_html_list( $extended_css );
		$controls_move = isset( $control_list['move'] ) ? $control_list['move'] : '';

		$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
		$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );
		$row_edit_clone_delete = '<span class="vc_row_edit_clone_delete">';

		if ( 'add' === $controls ) {
			return $controls_start . $control_list['add'] . $controls_end;
		}
		if ( $allAccess ) {
			$copypaste = vc_get_template( 'editors/partials/backend_copypaste_control.tpl.php' );
			$row_edit_clone_delete .= $control_list['delete'] . $copypaste . $control_list['clone'] . $control_list['edit'];
		} elseif ( $editAccess ) {
			$row_edit_clone_delete .= $control_list['edit'];
		}
		$row_edit_clone_delete .= '</span>';

		if ( $allAccess ) {
			$output .= '<div>' . $controls_move . $control_list['add'] . '</div>' . $row_edit_clone_delete . $controls_end;
		} elseif ( $editAccess ) {
			$output .= $row_edit_clone_delete . $controls_end;
		} else {
			$output .= $row_edit_clone_delete . $controls_end;
		}

		return $output;
	}

	/**
	 * Get admin output.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function contentAdmin( $atts, $content = null ) { // phpcs:ignore:CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		$width = '';
		$atts = shortcode_atts( $this->predefined_atts, $atts );

		$output = '';

		$column_controls = $this->getColumnControls();

		$output .= '<div data-element_type="' . $this->settings['base'] . '" class="' . $this->cssAdminClass() . '">';
		$output .= str_replace( '%column_size%', 1, $column_controls );
		$output .= '<div class="wpb_element_wrapper">';
		if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {
			$markup = $this->settings['custom_markup'];
			$output .= $this->customMarkup( $markup );
		} else {
			$output .= '<div ' . $this->containerHtmlBlockParams( $width, 1 ) . '>';
			$output .= do_shortcode( shortcode_unautop( $content ) );
			$output .= '</div>';
		}
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
		if ( $this->backened_editor_prepend_controls ) {
			$output .= $this->getColumnControls( 'add', 'vc_section-bottom-controls bottom-controls' );
		}
		$output .= '</div>';

		return $output; // nosemgrep - we already escaped everything on this step.
	}
}
