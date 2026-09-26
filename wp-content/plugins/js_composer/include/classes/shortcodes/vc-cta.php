<?php
/**
 * Class that handles specific [vc_cta] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_cta.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Cta
 *
 * @since 4.5
 */
class WPBakeryShortCode_Vc_Cta extends WPBakeryShortCode {

	/**
	 * Template variables list.
	 *
	 * @var array
	 */
	protected $template_vars = [];

	/**
	 * Get CSS file names for vc_cta shortcode.
	 *
	 * @since 9.0
	 * @return array
	 */
	public function get_shortcode_css_files() {
		return [ 'vc_call_to_action3', 'vc_cta3_editform' ];
	}

	/**
	 * Build element template variables.
	 *
	 * @param array $atts
	 * @param string $content
	 * @throws \Exception
	 */
	public function buildTemplate( $atts, $content ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh, CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		$output = [];
		$inline_css = [];

		$main_wrapper_classes = [ 'vc_cta3' ];
		$container_classes = [];
		if ( ! empty( $atts['el_class'] ) ) {
			$main_wrapper_classes[] = $atts['el_class'];
		}
		if ( ! empty( $atts['style'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-style-' . $atts['style'];
		}
		if ( ! empty( $atts['shape'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-shape-' . $atts['shape'];
		}
		if ( ! empty( $atts['txt_align'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-align-' . $atts['txt_align'];
		}
		if ( ! empty( $atts['i_on_border'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-icons-on-border';
		}
		if ( ! empty( $atts['i_size'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-icon-size-' . $atts['i_size'];
		}
		if ( ! empty( $atts['i_background_style'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-icons-in-box';
		}

		if ( ! empty( $atts['add_icon'] ) ) {
			$output[ 'icons-' . $atts['i_position'] ] = $this->getVcIcon( $atts );
			$main_wrapper_classes[] = 'vc_cta3-icons-' . $atts['i_position'];
		}

		if ( ! empty( $atts['add_button'] ) ) {
			$output[ 'actions-' . $atts['btn_position'] ] = $this->getButton( $atts );
			$main_wrapper_classes[] = 'vc_cta3-actions-' . $atts['btn_position'];
		}

		if ( ! empty( $atts['css_animation'] ) ) {
			$main_wrapper_classes[] = $this->getCSSAnimation( $atts['css_animation'] );
		}

		if ( ! empty( $atts['css'] ) ) {
			$main_wrapper_classes[] = vc_shortcode_custom_css_class( $atts['css'] );
		}

		$output['content'] = wpb_js_remove_wpautop( $content, true );
		$output['heading1'] = $this->getHeading( 'h2', $atts );
		$output['heading2'] = $this->getHeading( 'h4', $atts );
		$output['css-class'] = $main_wrapper_classes;
		$output['container-class'] = $container_classes;
		$output['inline-css'] = $this->get_container_color_css( $atts );
		$output['section-css'] = $this->get_section_width_css( $atts );
		$output['text-css'] = $this->get_text_css( $atts );
		$this->template_vars = $output;
	}

	/**
	 * B.C prior 9.0
	 *
	 * @depecated
	 * @param array $atts
	 * @return array
	 */
	public function get_text_css( $atts ) {
		$out = [];
		if ( isset( $atts['text_color'] ) ) {
			$out['color'] = vc_get_css_color( 'color', $atts['text_color'] );
		}

		return $out;
	}

	/**
	 * Get main wrapper container styles.
	 *
	 * @since 9.0
	 * @param array|null $atts
	 * @return array
	 */
	public function get_container_color_css( $atts ): array {
		$style = $this->get_element_style_params( $atts );

		switch ( $style ) {
			case 'flat':
				return $this->get_flat_style_css( $atts );
			case '3d':
				return $this->get_3d_style_css( $atts );
			case 'outline':
				return $this->get_outline_style_css( $atts );
			case 'custom':
				return $this->get_custom_style_css( $atts );
			default:
				return [];
		}
	}

	/**
	 * Get container with css.
	 *
	 * @since 9.0
	 * @param array|null $atts
	 * @return array
	 */
	public function get_section_width_css( $atts ): array {
		$css = [];

		if ( isset( $atts['el_width'] ) ) {
			$css[] = 'width:' . esc_attr( $atts['el_width'] ) . '%;';
		}

		return $css;
	}

	/**
	 * Get container CSS for flat style.
	 *
	 * @since 9.0
	 * @param array $atts
	 * @return array
	 */
	protected function get_flat_style_css( $atts ): array {
		$output = [];
		if ( ! empty( $atts['custom_background'] ) ) {
			$output[] = vc_get_css_color( 'background-color', $atts['custom_background'] );
		} else {
			$output[] = vc_get_css_color( 'background-color', '#f0f0f0' );
		}
		return $output;
	}

	/**
	 * Get container CSS for 3d style.
	 *
	 * @since 9.0
	 * @param array $atts
	 * @return array
	 */
	protected function get_3d_style_css( $atts ): array {
		$output = [];
		if ( ! empty( $atts['custom_background'] ) ) {
			$output[] = vc_get_css_color( 'background-color', $atts['custom_background'] );
		} else {
			$output[] = vc_get_css_color( 'background-color', '#f0f0f0' );
		}
		if ( ! empty( $atts['custom_border'] ) ) {
			$output[] = 'box-shadow: 0 5px 0 ' . esc_attr( $atts['custom_border'] ) . ';';
		} else {
			$output[] = 'box-shadow: 0 5px 0 #d4d4d4;';
		}

		return $output;
	}

	/**
	 * Get container CSS for outline style.
	 *
	 * @since 9.0
	 * @param array $atts
	 * @return array
	 */
	protected function get_outline_style_css( $atts ): array {
		$output = [ 'background-color: transparent;' ];
		if ( ! empty( $atts['custom_border'] ) ) {
			$output[] = vc_get_css_color( 'border-color', $atts['custom_border'] );
		} else {
			$output[] = 'border-color: #d4d4d4;';
		}
		return $output;
	}

	/**
	 * Get container CSS for custom style.
	 *
	 * @since 9.0
	 * @param array $atts
	 * @return array
	 */
	protected function get_custom_style_css( $atts ): array {
		$output = [];
		if ( ! empty( $atts['custom_background'] ) ) {
			$output[] = vc_get_css_color( 'background-color', $atts['custom_background'] );
		} else {
			$output[] = vc_get_css_color( 'background-color', '#f0f0f0' );
		}
		if ( ! empty( $atts['custom_border'] ) ) {
			$output[] = vc_get_css_color( 'border-color', $atts['custom_border'] );
		}
		return $output;
	}


	/**
	 * Get param style.
	 *
	 * @since 9.0
	 * @param array $atts
	 * @return string
	 */
	public function get_element_style_params( $atts ): string {
		if ( ! isset( $atts['style'] ) ) {
			$style = 'classic';
		} else {
			$style = $atts['style'];
		}

		return $style;
	}

	/**
	 * Get heading styles.
	 *
	 * @since 9.0
	 * @param array|null $atts
	 * @return array
	 */
	public function get_heading_css( $atts ): array {
		$output = [];
		$style = $this->get_element_style_params( $atts );

		switch ( $style ) {
			case 'classic':
			case 'outline':
			case 'custom':
			case 'flat':
			case '3d':
				if ( ! empty( $atts['custom_text'] ) ) {
					$output[] = vc_get_css_color( 'color', $atts['custom_text'] );
				} else {
					$output[] = vc_get_css_color( 'color', '#666' );
				}
				break;
		}

		return $output;
	}

	/**
	 * Get element heading.
	 *
	 * @param string $tag
	 * @param array $atts
	 * @return string
	 * @throws \Exception
	 */
	public function getHeading( $tag, $atts ) {
		if ( ! isset( $atts[ $tag ] ) || '' === trim( $atts[ $tag ] ) ) {
			return '';
		}
		if ( isset( $atts[ 'use_custom_fonts_' . $tag ] ) && 'true' === $atts[ 'use_custom_fonts_' . $tag ] ) {
			$custom_heading = wpbakery()->getShortCode( 'vc_custom_heading' );
			$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_custom_heading', $atts, $tag . '_' );
			$data['font_container'] = implode( '|', array_filter( [
				'tag:' . $tag,
				$data['font_container'],
			] ) );
			$data['text'] = $atts[ $tag ]; // provide text to shortcode.

			$output = $custom_heading->render( array_filter( $data ) );
		} else {
			$inline_css_string = '';
			$inline_css = $this->get_heading_css( $atts );
			if ( ! empty( $inline_css ) ) {
				$inline_css_string = ' style="' . implode( '', $inline_css ) . '"';
			}

			$output = '<' . $tag . $inline_css_string . '>' . wp_kses_post( $atts[ $tag ] ) . '</' . $tag . '>';
		}

		return $output;
	}

	/**
	 * Render element shortcode button as independent shortcode.
	 *
	 * @param array $atts
	 * @return string
	 * @throws \Exception
	 */
	public function getButton( $atts ) {
		$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_btn', $atts, 'btn_' );
		if ( $data ) {
			$btn = wpbakery()->getShortCode( 'vc_btn' );
			if ( is_object( $btn ) ) {
				return '<div class="vc_cta3-actions">' . $btn->render( array_filter( $data ) ) . '</div>';
			}
		}

		return '';
	}

	/**
	 * Get element icon output.
	 *
	 * @param array $atts
	 * @return string
	 * @throws \Exception
	 */
	public function getVcIcon( $atts ) {

		if ( empty( $atts['i_type'] ) ) {
			$atts['i_type'] = 'fontawesome';
		}
		$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_icon', $atts, 'i_' );
		if ( $data ) {
			$icon = wpbakery()->getShortCode( 'vc_icon' );
			if ( is_object( $icon ) ) {
				return '<div class="vc_cta3-icons">' . $icon->render( array_filter( $data ) ) . '</div>';
			}
		}

		return '';
	}

	/**
	 * Get template variable list.
	 *
	 * @param string $name
	 * @return mixed|string
	 */
	public function getTemplateVariable( $name ) {
		if ( is_array( $this->template_vars ) && isset( $this->template_vars[ $name ] ) ) {

			return $this->template_vars[ $name ];
		}

		return '';
	}
}
