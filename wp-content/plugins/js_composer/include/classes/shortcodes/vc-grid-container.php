<?php
/**
 * Class that handles specific [vc_grid_container] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_grid_container.php
 *
 * @since 8.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Grid_Container
 *
 * @since 8.7
 */
class WPBakeryShortCode_Vc_Grid_Container extends WPBakeryShortCode {
	/**
	 * Get shortcode inline html.
	 *
	 * @since 8.7
	 *
	 * @param array $atts
	 * @param null $content
	 * @return string
	 * @throws \Exception
	 */
	public function content( $atts, $content = null ) {
		return $this->loadTemplate( $atts, $content ); // nosemgrep - escaping handled inside templates.
	}

	/**
	 * Get Grid container Backend editor output.
	 *
	 * @since 8.7
	 *
	 * @param array $atts
	 * @param null $content
	 * @return string
	 * @throws Exception
	 */
	public function contentAdmin( $atts, $content = null ) {
		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		$output = '';
		$item_controls = $this->getItemControls( $this->settings( 'controls' ) );

		$output .= '<div data-element_type="' . esc_attr( $this->settings['base'] ) . '" class="' . esc_attr( $this->cssAdminClass() ) . '" style="' . esc_attr( $this->cssAdminStyles( $atts ) ) . '">';
		$output .= $item_controls;
		$output .= '<div class="wpb_element_wrapper">';
		$output .= '<div class="vc_grid_container vc_container_for_children">';
		if ( '' === $content && ! empty( $this->settings['default_content_in_template'] ) ) {
			$output .= do_shortcode( shortcode_unautop( $this->settings['default_content_in_template'] ) );
		} else {
			$output .= do_shortcode( shortcode_unautop( $content ) );

		}
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Returns the CSS class for the grid container in the backend editor.
	 *
	 * @since 8.7
	 *
	 * @return string CSS class name.
	 * @throws \Exception
	 */
	public function cssAdminClass() {
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? ' wpb_sortable' : ' ' );

		return 'wpb_' . $this->settings['base'] . $sortable . ' ' . ( ! empty( $this->settings['class'] ) ? ' ' . $this->settings['class'] : '' );
	}

	/**
	 * Get style attribute value.
	 *
	 * @since 8.7
	 *
	 * @param array $atts
	 * @return string
	 */
	public function cssAdminStyles( $atts = [] ) {
		$value = '--grid-cols: 1'; // default value.
		if ( isset( $atts['columns'] ) ) {
			$value = '--grid-cols: ' . $atts['columns'];
		}
		return $value;
	}

	/**
	 * Returns the HTML for the grid container item controls in the backend editor.
	 *
	 * @since 8.7
	 *
	 * @param mixed $controls Controls to display (array or string).
	 * @return string HTML output for controls.
	 * @throws \Exception
	 */
	public function getItemControls( $controls ) {
		$output = '<div class="vc_controls vc_controls-row controls_row vc_clearfix">';
		$controls_end = '</div>';

		$edit_access = vc_user_access_check_shortcode_edit( $this->shortcode );
		$all_access = vc_user_access_check_shortcode_all( $this->shortcode );

		if ( is_array( $controls ) && ! empty( $controls ) ) {
			$output .= $this->buildControlsFromArray( $controls, $edit_access, $all_access );
		} elseif ( is_string( $controls ) ) {
			$output .= $this->buildControlsFromString( $controls, $edit_access, $all_access );
		} else {
			$output .= $this->buildDefaultControls( $edit_access, $all_access );
		}

		$output .= $controls_end;
		return $output;
	}

	/**
	 * Builds HTML for controls from an array.
	 *
	 * @since 8.7
	 *
	 * @param array $controls List of control names.
	 * @param bool $edit_access Edit access flag.
	 * @param bool $all_access Full access flag.
	 * @return string HTML for controls.
	 */
	public function buildControlsFromArray( $controls, $edit_access, $all_access ) {
		$html = '';
		foreach ( $controls as $control ) {
			$html .= $this->getControlHtml( $control, $edit_access, $all_access );
		}
		return $html;
	}

	/**
	 * Builds HTML for a single control from a string.
	 *
	 * @since 8.7
	 *
	 * @param string $control Control name.
	 * @param bool $edit_access Edit access flag.
	 * @param bool $all_access Full access flag.
	 * @return string HTML for the control.
	 */
	public function buildControlsFromString( $control, $edit_access, $all_access ) {
		return $this->getControlHtml( $control, $edit_access, $all_access );
	}

	/**
	 * Builds default controls HTML if none are provided.
	 *
	 * @since 8.7
	 *
	 * @param bool $edit_access Edit access flag.
	 * @param bool $all_access Full access flag.
	 * @return string HTML for default controls.
	 */
	public function buildDefaultControls( $edit_access, $all_access ) {
		$controls_delete = $this->getControlHtml( 'delete', $edit_access, $all_access );
		$controls_paste = $this->getControlHtml( 'paste', $edit_access, $all_access );
		$controls_copy = $this->getControlHtml( 'copy', $edit_access, $all_access );
		$controls_clone = $this->getControlHtml( 'clone', $edit_access, $all_access );
		$controls_edit = $this->getControlHtml( 'edit', $edit_access, $all_access );
		$controls_toggle = $this->getControlHtml( 'toggle', $edit_access, $all_access );
		$controls_move = $this->getControlHtml( 'move', $edit_access, $all_access );

		$row_edit_clone_delete = '<span class="vc_row_edit_clone_delete">';
		if ( $all_access ) {
			$row_edit_clone_delete .= $controls_delete . $controls_paste . $controls_copy . $controls_clone . $controls_edit;
		} elseif ( $edit_access ) {
			$row_edit_clone_delete .= $controls_edit;
		}
		$row_edit_clone_delete .= $controls_toggle . '</span>';

		if ( $all_access ) {
			return '<div>' . $controls_move . '</div>' . $row_edit_clone_delete;
		}
		return $row_edit_clone_delete;
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
		return [
			'move' => [
				'classes' => 'column_move vc_column-move',
				'title'   => __( 'Drag Grid container to reorder', 'js_composer' ),
				'icon'    => 'vc-c-param-group-dragndrop',
			],
			'delete' => [
				'classes' => 'column_delete vc_column-delete',
				'title'   => __( 'Delete Grid container', 'js_composer' ),
				'icon'    => 'vc-c-trash',
			],
			'clone' => [
				'classes' => 'column_clone vc_column-clone',
				'title'   => __( 'Clone Grid container', 'js_composer' ),
				'icon'    => 'vc-c-icon-clone',
			],
			'copy' => [
				'classes' => 'column_copy vc_column-copy',
				'title'   => __( 'Copy Grid container', 'js_composer' ),
				'icon'    => 'vc-c-icon-copy',
			],
			'paste' => [
				'classes' => 'column_paste vc_column-paste',
				'title'   => __( 'Paste', 'js_composer' ),
				'icon'    => 'vc-c-icon-paste',
			],
			'edit' => [
				'classes' => 'column_edit vc_column-edit',
				'title'   => __( 'Edit Grid container', 'js_composer' ),
				'icon'    => 'vc-c-edit',
			],
			'toggle' => [
				'classes' => 'column_toggle vc_column-toggle',
				'title'   => __( 'Toggle Grid container', 'js_composer' ),
				'icon'    => 'vc-c-icon-arrow_drop_down',
			],
		];
	}

	/**
	 * Returns HTML for a specific control based on access.
	 *
	 * @since 8.7
	 *
	 * @param string $control Control name.
	 * @param bool $edit_access Edit access flag.
	 * @param bool $all_access Full access flag.
	 * @return string HTML for the control or empty string.
	 */
	public function getControlHtml( $control, $edit_access, $all_access ) {
		$controls_map = $this->get_column_controls_html_list();

		if ( ! isset( $controls_map[ $control ] ) ) {
			return '';
		}

		if ( ( $edit_access && 'edit' === $control ) || $all_access || 'toggle' === $control ) {
			if ( 'move' === $control ) {
				$move_access = vc_user_access()->part( 'dragndrop' )->checkStateAny( true, null )->get();
				return $move_access ? $controls_map[ $control ] : '';
			}
			return $controls_map[ $control ];
		}
		return '';
	}

	/**
	 * Get shortcode output.
	 *
	 * @param array $atts
	 * @param null|string $content
	 *
	 * @return mixed
	 */
	public function loadTemplate( $atts, $content = null ) {
		$disable_element = isset( $atts['disable_element'] ) ? $atts['disable_element'] : '';
		if ( $disable_element && ! vc_is_page_editable() ) {
			return '';
		}

		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		wp_enqueue_script( 'wpb_composer_front_js' );

		return parent::loadTemplate( $atts, $content );
	}

	/**
	 * Output wrapper attributes.
	 *
	 * @since 8.7
	 * @param array $atts
	 */
	public function output_wrapper_attributes( $atts ) {
		$el_class = $css = $el_id = $css_animation = $disable_element = '';
		extract( $atts );

		/**
		 * Extracted attributes.
		 *
		 * @var string $el_class
		 * @var string $css
		 * @var string $el_id
		 * @var string $css_animation
		 * @var string $disable_element
		 * @var string $rows
		 */

		$el_class = $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );

		$css_classes = [
			'vc_grid_container',
			$el_class,
			vc_shortcode_custom_css_class( $css ),
		];

		if ( 'yes' === $disable_element && vc_is_page_editable() ) {
			$css_classes[] = 'vc_hidden-xl vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
		}

		$wrapper_attributes = [];
		if ( ! empty( $el_id ) ) {
			$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
		}

		$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( array_unique( $css_classes ) ) ), $this->settings['base'], $atts ) );
		$rows = isset( $atts['rows'] ) ? '--grid-rows: ' . $atts['rows'] . ';' : '';
		$columns = isset( $atts['columns'] ) ? '--grid-cols: ' . $atts['columns'] . ';' : '';
		$row_gap = isset( $atts['row_gap'] ) ? '--row-gap: ' . $atts['row_gap'] . ';' : '';
		$col_gap = isset( $atts['col_gap'] ) ? '--col-gap: ' . $atts['col_gap'] . ';' : '';
		$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';
		if ( ! empty( $rows ) || ! empty( $columns ) || ! empty( $row_gap ) || ! empty( $col_gap ) ) {
			$wrapper_attributes[] = 'style="' . esc_attr( $rows ) . esc_attr( $columns ) . esc_attr( $col_gap ) . esc_attr( $row_gap ) . '"';
		}

		echo implode( ' ', $wrapper_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
