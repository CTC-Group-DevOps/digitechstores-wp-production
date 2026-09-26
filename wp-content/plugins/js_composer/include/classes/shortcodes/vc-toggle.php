<?php
/**
 * Class that handles specific [vc_toggle] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_toggle.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Toggle
 */
class WPBakeryShortCode_Vc_Toggle extends WPBakeryShortCode {
	/**
	 * Override default title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function outputTitle( $title ) {
		return '';
	}

	/**
	 * Get element heading.
	 *
	 * @param array $atts
	 * @return string
	 * @throws \Exception
	 */
	public function getHeading( $atts ) {
		if ( isset( $atts['use_custom_heading'] ) && 'true' === $atts['use_custom_heading'] ) {
			$custom_heading = wpbakery()->getShortCode( 'vc_custom_heading' );

			$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_custom_heading', $atts, 'custom_' );
			$data['text'] = $atts['title'];

			return $custom_heading->render( array_filter( $data ) );
		} else {
			return '<h4>' . esc_html( $atts['title'] ) . '</h4>';
		}
	}

	/**
	 * Resolve the CSS color class for the toggle element.
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
			return Vc_Color_Helper::hexClass( 'vc_toggle_hex_', $color );
		}

		// b.c prior to 9.0.
		return 'vc_toggle_color_' . $color;
	}

	/**
	 * Build an inline <style> block for a custom hex color value.
	 *
	 * Returns an empty string when $color is not a valid hex value
	 * or when the style does not render an icon (text_only).
	 *
	 * @since 9.0
	 * @param string $color
	 * @param string $style
	 * @param bool   $inverted
	 * @return string
	 */
	public function buildHexColorStyle( $color, $style, $inverted ) {
		if ( ! Vc_Color_Helper::isHexColor( $color ) ) {
			return '';
		}
		if ( 'text_only' === $style ) {
			return '';
		}

		if ( $this->isDuplicateHexColorStyle( $color, $style, $inverted ) ) {
			return '';
		}

		$hex = Vc_Color_Helper::normalizeHex( ltrim( $color, '#' ) );
		$r   = hexdec( substr( $hex, 0, 2 ) );
		$g   = hexdec( substr( $hex, 2, 2 ) );
		$b   = hexdec( substr( $hex, 4, 2 ) );

		$base_color   = esc_attr( $color );
		$hover_color  = esc_attr( Vc_Color_Helper::lightenRgb( $r, $g, $b, 0.2 ) );
		$border_color = esc_attr( Vc_Color_Helper::darkenRgb( $r, $g, $b, 0.9 ) );
		$selector     = '.' . Vc_Color_Helper::hexClass( 'vc_toggle_hex_', $color );

		$rules = $this->buildStyleRulesForStyle( $selector, $style, $inverted, $base_color, $hover_color, $border_color );

		$tag = 'style';
		return '<' . $tag . '>' . $rules . '</' . $tag . '>';
	}

	/**
	 * Return true if a style block for this color/style/inverted combination was already emitted.
	 *
	 * Registers the combination on first call so subsequent calls return true.
	 *
	 * @since 9.0
	 * @param string $color
	 * @param string $style
	 * @param bool   $inverted
	 * @return bool
	 */
	private function isDuplicateHexColorStyle( $color, $style, $inverted ) {
		static $emitted = [];
		$cache_key = md5( $color . '|' . $style . '|' . ( $inverted ? '1' : '0' ) );
		if ( isset( $emitted[ $cache_key ] ) ) {
			return true;
		}
		$emitted[ $cache_key ] = true;
		return false;
	}

	/**
	 * Dispatch style rule generation to the correct per-style method.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param string $style
	 * @param bool   $inverted
	 * @param string $base_color
	 * @param string $hover_color
	 * @param string $border_color
	 * @return string
	 */
	private function buildStyleRulesForStyle( $selector, $style, $inverted, $base_color, $hover_color, $border_color ) {
		switch ( $style ) {
			case 'simple':
				return $this->buildSimpleStyleRules( $selector, $base_color, $hover_color );
			case 'default':
				return $this->buildDefaultStyleRules( $selector, $base_color, $hover_color, $border_color );
			case 'arrow':
				return $this->buildArrowStyleRules( $selector, $base_color, $hover_color );
			default: // round, rounded, square — and their inverted (outline) variants.
				return $inverted
					? $this->buildInvertedVariantStyleRules( $selector, $base_color, $hover_color )
					: $this->buildSolidVariantStyleRules( $selector, $base_color, $hover_color );
		}
	}

	/**
	 * CSS rules for the "simple" toggle style.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param string $base_color
	 * @param string $hover_color
	 * @return string
	 */
	private function buildSimpleStyleRules( $selector, $base_color, $hover_color ) {
		$css  = $selector . '.vc_toggle_simple .vc_toggle_icon{background-color:transparent;border-color:transparent}';
		$css .= $selector . '.vc_toggle_simple .vc_toggle_icon::before,' . $selector . '.vc_toggle_simple .vc_toggle_icon::after{background-color:' . $base_color . '}';
		$css .= $selector . '.vc_toggle_simple .vc_toggle_title:hover .vc_toggle_icon{background-color:transparent}';
		$css .= $selector . '.vc_toggle_simple .vc_toggle_title:hover .vc_toggle_icon::before,' . $selector . '.vc_toggle_simple .vc_toggle_title:hover .vc_toggle_icon::after{background-color:' . $hover_color . '}';
		return $css;
	}

	/**
	 * CSS rules for the "default" toggle style.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param string $base_color
	 * @param string $hover_color
	 * @param string $border_color
	 * @return string
	 */
	private function buildDefaultStyleRules( $selector, $base_color, $hover_color, $border_color ) {
		$css  = $selector . '.vc_toggle_default .vc_toggle_icon{background:' . $base_color . ';border-color:' . $border_color . '}';
		$css .= $selector . '.vc_toggle_default .vc_toggle_icon::before{border-color:' . $border_color . ';background:' . $base_color . '}';
		$css .= $selector . '.vc_toggle_default .vc_toggle_icon::after{background:' . $base_color . '}';
		$css .= $selector . '.vc_toggle_default .vc_toggle_title:hover .vc_toggle_icon{background:' . $hover_color . ';border-color:' . $base_color . '}';
		$css .= $selector . '.vc_toggle_default .vc_toggle_title:hover .vc_toggle_icon::before{border-color:' . $base_color . ';background:' . $hover_color . '}';
		$css .= $selector . '.vc_toggle_default .vc_toggle_title:hover .vc_toggle_icon::after{background:' . $hover_color . '}';
		return $css;
	}

	/**
	 * CSS rules for the "arrow" toggle style.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param string $base_color
	 * @param string $hover_color
	 * @return string
	 */
	private function buildArrowStyleRules( $selector, $base_color, $hover_color ) {
		$css  = $selector . '.vc_toggle_arrow .vc_toggle_icon{background:transparent}';
		$css .= $selector . '.vc_toggle_arrow .vc_toggle_icon::before,' . $selector . '.vc_toggle_arrow .vc_toggle_icon::after{border-color:' . $base_color . ';background:transparent}';
		$css .= $selector . '.vc_toggle_arrow .vc_toggle_title:hover .vc_toggle_icon{background:transparent}';
		$css .= $selector . '.vc_toggle_arrow .vc_toggle_title:hover .vc_toggle_icon::before,' . $selector . '.vc_toggle_arrow .vc_toggle_title:hover .vc_toggle_icon::after{border-color:' . $hover_color . ';background:transparent}';
		return $css;
	}

	/**
	 * CSS rules for solid (filled) variants: round, rounded, square.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param string $base_color
	 * @param string $hover_color
	 * @return string
	 */
	private function buildSolidVariantStyleRules( $selector, $base_color, $hover_color ) {
		$css  = $selector . ' .vc_toggle_icon{background-color:' . $base_color . ';border-color:transparent}';
		$css .= $selector . ' .vc_toggle_icon::before,' . $selector . ' .vc_toggle_icon::after{background-color:#fff}';
		$css .= $selector . ' .vc_toggle_title:hover .vc_toggle_icon{background-color:' . $hover_color . '}';
		$css .= $selector . ' .vc_toggle_title:hover .vc_toggle_icon::before,' . $selector . ' .vc_toggle_title:hover .vc_toggle_icon::after{background-color:#fff}';
		return $css;
	}

	/**
	 * CSS rules for inverted (outline) variants: round_outline, rounded_outline, square_outline.
	 *
	 * @since 9.0
	 * @param string $selector
	 * @param string $base_color
	 * @param string $hover_color
	 * @return string
	 */
	private function buildInvertedVariantStyleRules( $selector, $base_color, $hover_color ) {
		$css  = $selector . '.vc_toggle_color_inverted .vc_toggle_icon{background-color:transparent;border-color:' . $base_color . '}';
		$css .= $selector . '.vc_toggle_color_inverted .vc_toggle_icon::before,' . $selector . '.vc_toggle_color_inverted .vc_toggle_icon::after{background-color:' . $base_color . '}';
		$css .= $selector . '.vc_toggle_color_inverted .vc_toggle_title:hover .vc_toggle_icon{background-color:transparent;border-color:' . $hover_color . '}';
		$css .= $selector . '.vc_toggle_color_inverted .vc_toggle_title:hover .vc_toggle_icon::before,' . $selector . '.vc_toggle_color_inverted .vc_toggle_title:hover .vc_toggle_icon::after{background-color:' . $hover_color . '}';
		return $css;
	}
}
