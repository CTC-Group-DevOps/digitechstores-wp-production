<?php
/**
 * Class that handles specific [vc_message] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_message.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Message
 */
class WPBakeryShortCode_Vc_Message extends WPBakeryShortCode {
	/**
	 * Get CSS file names for vc_message shortcode.
	 *
	 * @since 9.0
	 * @return string
	 */
	public function get_shortcode_css_files() {
		return 'vc_message_box';
	}

	/**
	 * Resolve the CSS color class for the message box element.
	 *
	 * Returns a hex-based unique class for custom hex colors,
	 * or a preset class for named palette colors.
	 *
	 * @since 9.0
	 * @param string $color
	 * @return string
	 */
	public function resolveColorClass( $color ) {
		if ( ! $color ) {
			return '';
		}
		if ( Vc_Color_Helper::isHexColor( $color ) ) {
			return Vc_Color_Helper::hexClass( 'vc_color-hex-', $color );
		}

		// b.c prior to 9.0.
		return 'vc_color-' . $color;
	}

	/**
	 * Build an inline <style> block reproducing the LESS color set for a hex value.
	 *
	 * Returns an empty string when $color is not a valid hex value.
	 *
	 * @since 9.0
	 * @param string $color
	 * @return string
	 */
	public function buildHexColorStyle( $color ) {
		if ( ! Vc_Color_Helper::isHexColor( $color ) ) {
			return '';
		}
		if ( $this->isDuplicateHexColorStyle( $color ) ) {
			return '';
		}

		$palette  = $this->buildHexColorPalette( $color );
		$selector = '.' . Vc_Color_Helper::hexClass( 'vc_color-hex-', $color );

		$css  = $this->buildBaseStyleRules( $selector, $palette );
		$css .= $this->buildSolidStyleRules( $selector, $palette );
		$css .= $this->buildOutlineStyleRules( $selector, $palette );
		$css .= $this->buildSolidIconStyleRules( $selector, $palette );
		$css .= $this->build3dStyleRules( $selector, $palette );

		return '<style>' . $css . '</style>';
	}

	/**
	 * Return true if a style block for this color was already emitted.
	 *
	 * @since 9.0
	 * @param string $color
	 * @return bool
	 */
	private function isDuplicateHexColorStyle( $color ) {
		static $emitted = [];
		if ( isset( $emitted[ $color ] ) ) {
			return true;
		}
		$emitted[ $color ] = true;
		return false;
	}

	/**
	 * Compute the derived color palette for a custom hex value.
	 *
	 * Values are already escaped for use in CSS attribute context.
	 *
	 * @since 9.0
	 * @param string $color
	 * @return array{icon:string,bg:string,border:string,text:string,shadow:string,contrast:string}
	 */
	private function buildHexColorPalette( $color ) {
		$hex = Vc_Color_Helper::normalizeHex( ltrim( $color, '#' ) );
		$r   = hexdec( substr( $hex, 0, 2 ) );
		$g   = hexdec( substr( $hex, 2, 2 ) );
		$b   = hexdec( substr( $hex, 4, 2 ) );

		return [
			'icon'     => esc_attr( '#' . $hex ),
			'bg'       => esc_attr( Vc_Color_Helper::lightenRgb( $r, $g, $b, 0.85 ) ),
			'border'   => esc_attr( Vc_Color_Helper::lightenRgb( $r, $g, $b, 0.7 ) ),
			'text'     => esc_attr( Vc_Color_Helper::darkenRgb( $r, $g, $b, 0.55 ) ),
			'shadow'   => esc_attr( Vc_Color_Helper::darkenRgb( $r, $g, $b, 0.9 ) ),
			'contrast' => esc_attr( '#fff' ),
		];
	}

	/**
	 * CSS rules for the standard message box: container colors and icon color.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param array  $palette
	 * @return string
	 */
	private function buildBaseStyleRules( $selector, $palette ) {
		$css  = $selector . '.vc_message_box{color:' . $palette['text'] . ';border-color:' . $palette['border'] . ';background-color:' . $palette['bg'] . '}';
		$css .= $selector . '.vc_message_box .vc_message_box-icon{color:' . $palette['icon'] . '}';
		return $css;
	}

	/**
	 * CSS rules for the "solid" message box variant.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param array  $palette
	 * @return string
	 */
	private function buildSolidStyleRules( $selector, $palette ) {
		$css  = $selector . '.vc_message_box-solid{color:' . $palette['contrast'] . ';border-color:transparent;background-color:' . $palette['icon'] . '}';
		$css .= $selector . '.vc_message_box-solid .vc_message_box-icon{color:' . $palette['contrast'] . '}';
		return $css;
	}

	/**
	 * CSS rules shared by the "outline" and "solid-icon" message box variants.
	 *
	 * Covers container colors and the inner icon color for both variants.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param array  $palette
	 * @return string
	 */
	private function buildOutlineStyleRules( $selector, $palette ) {
		$css  = $selector . '.vc_message_box-outline,' . $selector . '.vc_message_box-solid-icon{color:' . $palette['text'] . ';border-color:' . $palette['icon'] . ';background-color:transparent}';
		$css .= $selector . '.vc_message_box-outline .vc_message_box-icon,' . $selector . '.vc_message_box-solid-icon .vc_message_box-icon{color:' . $palette['icon'] . '}';
		return $css;
	}

	/**
	 * CSS override for the "solid-icon" variant's inner icon (filled background).
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param array  $palette
	 * @return string
	 */
	private function buildSolidIconStyleRules( $selector, $palette ) {
		return $selector . '.vc_message_box-solid-icon .vc_message_box-icon{color:' . $palette['contrast'] . ';background-color:' . $palette['icon'] . '}';
	}

	/**
	 * CSS rules for the "3d" message box drop shadow.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param array  $palette
	 * @return string
	 */
	private function build3dStyleRules( $selector, $palette ) {
		return $selector . '.vc_message_box-3d{box-shadow:0 5px 0 ' . $palette['shadow'] . '}';
	}

	/**
	 * Convert attributes to message box.
	 *
	 * @param array $atts
	 * @return mixed
	 */
	public static function convertAttributesToMessageBox2( $atts ) {
		if ( isset( $atts['style'] ) ) {
			if ( '3d' === $atts['style'] ) {
				$atts['message_box_style'] = '3d';
				$atts['style'] = 'rounded';
			} elseif ( 'outlined' === $atts['style'] ) {
				$atts['message_box_style'] = 'outline';
				$atts['style'] = 'rounded';
			} elseif ( 'square_outlined' === $atts['style'] ) {
				$atts['message_box_style'] = 'outline';
				$atts['style'] = 'square';
			}
		}

		return $atts;
	}

	/**
	 * Override default title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function outputTitle( $title ) {
		return '';
	}
}
