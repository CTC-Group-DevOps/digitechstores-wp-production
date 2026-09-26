<?php
/**
 * Class that handles specific [vc_tta_accordion] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_tta_accordion.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Tta_Accordion
 */
class WPBakeryShortCode_Vc_Tta_Accordion extends WPBakeryShortCodesContainer {
	/**
	 * CSS settings for controls.
	 *
	 * @var string
	 */
	protected $controls_css_settings = 'out-tc vc_controls-content-widget';

	/**
	 * List of controls available.
	 *
	 * @var array
	 */
	protected $controls_list = [
		'add',
		'edit',
		'clone',
		'copypaste',
		'delete',
	];

	/**
	 * Template variables.
	 *
	 * @var array
	 */
	protected $template_vars = [];

	/**
	 * Layout type.
	 *
	 * @var string
	 */
	public $layout = 'accordion';

	/**
	 * Content of the accordion.
	 *
	 * @var mixed
	 */
	protected $content;

	/**
	 * Active class name.
	 *
	 * @var string
	 */
	public $activeClass = 'vc_active';

	/**
	 * Section class instance.
	 *
	 * @var WPBakeryShortCode_Vc_Tta_Section
	 */
	protected $sectionClass;

	/**
	 * Non-draggable class name.
	 *
	 * @var string
	 */
	public $nonDraggableClass = 'vc-non-draggable-container';

	/**
	 * Get name.
	 *
	 * @return mixed|string
	 */
	public function getFileName() {
		return 'vc_tta_global';
	}

	/**
	 * Get container content class.
	 *
	 * @return string
	 */
	public function containerContentClass() {
		return 'vc_container_for_children vc_clearfix';
	}

	/**
	 * Reset var values.
	 *
	 * @param array $atts
	 * @param string $content
	 */
	public function resetVariables( $atts, $content ) {
		$this->atts = $atts;
		$this->content = $content;
		$this->template_vars = [];
	}

	/**
	 * Set tta info data.
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function setGlobalTtaInfo() {
		$sectionClass = wpbakery()->getShortCode( 'vc_tta_section' )->shortcodeClass();
		$this->sectionClass = $sectionClass;

		// WPBakeryShortCode_Vc_Tta_Section $sectionClass - instance of section class.
		if ( is_object( $sectionClass ) ) {
			VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Section' );
			WPBakeryShortCode_Vc_Tta_Section::$tta_base_shortcode = $this;
			WPBakeryShortCode_Vc_Tta_Section::$self_count = 0;
			WPBakeryShortCode_Vc_Tta_Section::$section_info = [];

			return true;
		}

		return false;
	}

	/**
	 * Override default getColumnControls to make it "simple"(blue), as single element has
	 *
	 * @param string $controls
	 * @param string $extended_css
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getColumnControls( $controls = 'full', $extended_css = '' ) {
		// we don't need containers bottom-controls for tabs.
		if ( false !== strpos( $extended_css, 'bottom-controls' ) ) {
			return '';
		}
		$column_controls = $this->getColumnControlsModular();

		return $output = $column_controls;
	}

	/**
	 * Get tta container classes.
	 *
	 * @return string
	 */
	public function getTtaContainerClasses() {
		$classes = [];
		$classes[] = 'vc_tta-container';

		return implode( ' ', apply_filters( 'vc_tta_container_classes', array_filter( $classes ), $this->getAtts() ) );
	}

	/**
	 * Add specific tta classes.
	 *
	 * @return string
	 */
	public function getTtaGeneralClasses() {
		$classes = [];
		$classes[] = 'vc_general';
		$classes[] = 'vc_tta';
		$classes[] = 'vc_tta-' . $this->layout;
		$classes[] = $this->getTemplateVariable( 'color' );
		$classes[] = $this->getTemplateVariable( 'style' );
		$classes[] = $this->getTemplateVariable( 'shape' );
		$classes[] = $this->getTemplateVariable( 'spacing' );
		$classes[] = $this->getTemplateVariable( 'gap' );
		$classes[] = $this->getTemplateVariable( 'c_align' );
		$classes[] = $this->getTemplateVariable( 'no_fill' );
		if ( isset( $this->atts['collapsible_all'] ) && 'true' === $this->atts['collapsible_all'] ) {
			$classes[] = 'vc_tta-o-all-clickable';
		}

		$pagination = isset( $this->atts['pagination_style'] ) ? trim( $this->atts['pagination_style'] ) : false;
		if ( $pagination && 'none' !== $pagination && strlen( $pagination ) > 0 ) {
			$classes[] = 'vc_tta-has-pagination';
		}

		// since 4.6.2.
		if ( isset( $this->atts['el_class'] ) ) {
			$classes[] = $this->getExtraClass( $this->atts['el_class'] );
		}

		return implode( ' ', apply_filters( 'vc_tta_accordion_general_classes', array_filter( $classes ), $this->getAtts() ) );
	}

	/**
	 * Retrieve tta pagination classes.
	 *
	 * @return string
	 */
	public function getTtaPaginationClasses() {
		$classes = [];
		$classes[] = 'vc_general';
		$classes[] = 'vc_pagination';

		if ( isset( $this->atts['pagination_style'] ) && strlen( $this->atts['pagination_style'] ) > 0 ) {
			$chunks = explode( '-', $this->atts['pagination_style'] );
			$classes[] = 'vc_pagination-style-' . $chunks[0];
			$classes[] = 'vc_pagination-shape-' . $chunks[1];
		}

		if ( isset( $this->atts['pagination_color'] ) && strlen( $this->atts['pagination_color'] ) > 0 ) {
			if ( $this->isColorCustom( $this->atts['pagination_color'] ) ) {
				// Custom colorpicker value (hex/rgb/etc). Color is applied via CSS variable.
				$classes[] = 'vc_pagination-color-custom';
			} else {
				$classes[] = 'vc_pagination-color-' . $this->atts['pagination_color'];
			}
		} else {
			$classes[] = 'vc_pagination-color-grey';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Get inline style for pagination wrapper.
	 *
	 * Returns a CSS custom property declaration when the pagination color is
	 * a custom value (hex/rgb/etc), so styles can read it via var(--vc-pagination-color).
	 *
	 * @return string
	 * @since 9.0
	 */
	public function getTtaPaginationStyle() {
		if (
			isset( $this->atts['pagination_color'] )
			&& strlen( $this->atts['pagination_color'] ) > 0
			&& $this->isColorCustom( $this->atts['pagination_color'] )
		) {
			return '--vc-pagination-color: ' . $this->atts['pagination_color'] . ';';
		}

		return '';
	}

	/**
	 * Get element wrapper attributes.
	 *
	 * @return string
	 */
	public function getWrapperAttributes() {
		$attributes = [];
		$attributes[] = 'class="' . esc_attr( $this->getTtaContainerClasses() ) . '"';
		$attributes[] = 'data-vc-action="' . ( 'true' === $this->atts['collapsible_all'] ? 'collapseAll' : 'collapse' ) . '"';

		$autoplay = isset( $this->atts['autoplay'] ) ? trim( $this->atts['autoplay'] ) : false;
		if ( $autoplay && 'none' !== $autoplay && intval( $autoplay ) > 0 ) {
			$autoplayAttr = wp_json_encode( [
				'delay' => intval( $autoplay ) * 1000,
			] );
			$attributes[] = 'data-vc-tta-autoplay="' . esc_attr( $autoplayAttr ) . '"';
		}
		if ( ! empty( $this->atts['el_id'] ) ) {
			$attributes[] = 'id="' . esc_attr( $this->atts['el_id'] ) . '"';
		}

		return implode( ' ', $attributes );
	}

	/**
	 * Get element template variables.
	 *
	 * @param string $initial
	 * @return mixed|string
	 */
	public function getTemplateVariable( $initial ) {
		if ( isset( $this->template_vars[ $initial ] ) ) {
			return $this->template_vars[ $initial ];
		} elseif ( method_exists( $this, 'getParam' . vc_studly( $initial ) ) ) {
			$this->template_vars[ $initial ] = $this->{'getParam' . vc_studly( $initial )}( $this->atts, $this->content );

			return $this->template_vars[ $initial ];
		}

		return '';
	}

	/**
	 * Get optional param color class.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string|null
	 */
	public function getParamColor( $atts, $content ) {
		$has_custom_override = $this->hasCustomColorOverride( $atts );

		if ( isset( $atts['color'] ) && strlen( $atts['color'] ) > 0 ) {
			if ( ! $this->isColorCustom( $atts['color'] ) ) {
				return 'vc_tta-color-' . esc_attr( $atts['color'] );
			}

			$palette_slug = $this->getPaletteColorSlug( $atts['color'] );
			if ( '' !== $palette_slug && ! $has_custom_override ) {
				return 'vc_tta-color-' . $palette_slug;
			}

			return 'vc_tta-color-custom';
		}

		// No `color` value but a custom override (active/title color) is provided. We still
		// need to emit the custom class so the CSS variable pipeline applies.
		if ( $has_custom_override ) {
			return 'vc_tta-color-custom';
		}

		return null;
	}

	/**
	 * Get legacy palette color slug matching the given custom color value.
	 *
	 * @param string $value
	 * @return string Dashed palette slug or empty string when there is no match.
	 * @since 9.0
	 */
	protected function getPaletteColorSlug( $value ) {
		$value = strtolower( trim( (string) $value ) );
		foreach ( vc_get_shared( 'colors-dashed' ) as $slug ) {
			if ( strtolower( vc_convert_vc_color( $slug ) ) === $value ) {
				return $slug;
			}
		}

		return '';
	}

	/**
	 * Check whether the color value is a custom colorpicker value.
	 *
	 * @param string $value
	 * @return bool
	 * @since 9.0
	 */
	protected function isColorCustom( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return false;
		}
		return (bool) preg_match( '/^(#|rgb|hsl)/i', $value );
	}

	/**
	 * Get inline style for the TTA general wrapper.
	 *
	 * Returns a CSS custom property declaration when the color attribute is
	 * a custom value (hex/rgb/etc), so styles can read it via var(--vc-tta-color).
	 *
	 * @return string
	 * @since 9.0
	 */
	public function getTtaGeneralStyle() {
		$declarations = array_merge(
			$this->getTtaColorDeclarations(),
			$this->getTtaUnitDeclaration( '--vc-tta-spacing', $this->atts['spacing'] ?? '' ),
			$this->getTtaUnitDeclaration( '--vc-tta-gap', $this->atts['gap'] ?? '' )
		);

		if ( empty( $declarations ) ) {
			return '';
		}

		return implode( '; ', $declarations ) . ';';
	}

	/**
	 * Build CSS custom property declarations for the TTA color attribute.
	 *
	 * @return array
	 * @since 9.0
	 */
	protected function getTtaColorDeclarations() {
		$declarations = $this->getBaseColorDeclarations();

		$title_color_map = [
			'inactive_title_color' => '--vc-tta-title-color',
			'active_title_color' => '--vc-tta-active-title-color',
		];
		foreach ( $title_color_map as $key => $property ) {
			$value = $this->atts[ $key ] ?? '';
			if ( $this->isColorCustom( $value ) ) {
				$declarations[] = $property . ': ' . $value;
			}
		}

		return $declarations;
	}

	/**
	 * Build CSS custom property declarations for the inactive/active background colors.
	 *
	 * @return array
	 * @since 9.0
	 */
	protected function getBaseColorDeclarations() {
		$is_accordion_outline = 'accordion' === $this->layout
			&& isset( $this->atts['style'] ) && 'outline' === $this->atts['style'];

		if ( $is_accordion_outline ) {
			return $this->getOutlineColorDeclarations();
		}

		return $this->getFilledColorDeclarations();
	}

	/**
	 * Build CSS custom property declarations for the outline style.
	 *
	 * @return array
	 * @since 9.0
	 */
	protected function getOutlineColorDeclarations() {
		$declarations = [];

		$outline_color = $this->atts['outline_color'] ?? '';
		if ( $this->isColorCustom( $outline_color ) ) {
			$declarations[] = '--vc-tta-color: ' . $outline_color;
			$declarations[] = '--vc-tta-color-contrast: ' . $this->getContrastColor( $outline_color );
		}

		return $declarations;
	}

	/**
	 * Build CSS custom property declarations for the filled styles.
	 *
	 * @return array
	 * @since 9.0
	 */
	protected function getFilledColorDeclarations() {
		$declarations = [];

		$color = $this->atts['color'] ?? '';
		if ( $this->isColorCustom( $color ) ) {
			$is_palette = '' !== $this->getPaletteColorSlug( $color ) && ! $this->hasCustomColorOverride( $this->atts );
			if ( ! $is_palette ) {
				$declarations[] = '--vc-tta-color: ' . $color;
				$declarations[] = '--vc-tta-color-contrast: ' . $this->getContrastColor( $color );
			}
		}

		$active_color = $this->atts['active_color'] ?? '';
		if ( $this->isColorCustom( $active_color ) ) {
			$declarations[] = '--vc-tta-active-color: ' . $active_color;
			$declarations[] = '--vc-tta-active-color-contrast: ' . $this->getContrastColor( $active_color );
		}

		return $declarations;
	}

	/**
	 * Whether a custom (colorpicker) active or title color override is set.
	 *
	 * When any of these is custom the wrapper must use the `vc_tta-color-custom`
	 * class so the CSS variable pipeline applies instead of a palette class.
	 *
	 * @param array $atts
	 * @return bool
	 * @since 9.0
	 */
	protected function hasCustomColorOverride( $atts ) {
		foreach ( [ 'active_color', 'active_title_color', 'inactive_title_color' ] as $key ) {
			if ( $this->isColorCustom( $atts[ $key ] ?? '' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Build a CSS custom property declaration for a unit-aware attribute.
	 *
	 * @param string $property CSS custom property name.
	 * @param string $value Raw attribute value.
	 * @return array
	 * @since 9.0
	 */
	protected function getTtaUnitDeclaration( $property, $value ) {
		$formatted = wpb_format_with_css_unit( $value );
		if ( empty( $formatted ) ) {
			$formatted = wpb_format_with_css_unit( 0 );
		}

		if ( '' === (string) $formatted || ! $this->isUnitValueCustom( $formatted ) ) {
			return [];
		}

		return [ $property . ': ' . $formatted ];
	}

	/**
	 * Resolve a readable contrast color (dark or light) for the given input.
	 *
	 * Mirrors the existing palette behaviour where dark backgrounds use `#fff`
	 * text and the light grey palette uses `#666`. Supports hex (#rgb, #rrggbb)
	 * and `rgb()/rgba()` notations; falls back to `#fff` when the value cannot
	 * be parsed.
	 *
	 * @param string $value
	 * @return string
	 * @since 9.0
	 */
	protected function getContrastColor( $value ) {
		$rgb = Vc_Color_Helper::hexToRgb( $value );
		if ( null === $rgb ) {
			return '#fff';
		}

		// Relative luminance (sRGB, simplified).
		$luminance = ( $rgb['R'] * 0.299 + $rgb['G'] * 0.587 + $rgb['B'] * 0.114 ) / 255;

		return $luminance > 0.7 ? '#666' : '#fff';
	}

	/**
	 * Get optional param style class.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string|null
	 */
	public function getParamStyle( $atts, $content ) {
		if ( isset( $atts['style'] ) && strlen( $atts['style'] ) > 0 ) {
			return 'vc_tta-style-' . esc_attr( $atts['style'] );
		}

		return null;
	}

	/**
	 * Get element title html.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string|null
	 */
	public function getParamTitle( $atts, $content ) {
		if ( isset( $atts['title'] ) && strlen( $atts['title'] ) > 0 ) {
			$tag = 'h2';
			if ( isset( $atts['title_tag'] ) ) {
				$tag = $atts['title_tag'];
			}

			return '<' . $tag . '>' . esc_html( $atts['title'] ) . '</' . $tag . '>';
		}

		return null;
	}

	/**
	 * Get element icon html.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string|null
	 */
	public function getParamContent( $atts, $content ) {
		$panelsContent = wpb_js_remove_wpautop( $content );
		if ( isset( $atts['c_icon'] ) && strlen( $atts['c_icon'] ) > 0 ) {
			$isPageEditable = vc_is_page_editable();
			if ( ! $isPageEditable ) {
				$panelsContent = str_replace( '{{{ control-icon }}}', '<i class="vc_tta-controls-icon vc_tta-controls-icon-' . $atts['c_icon'] . '"></i>', $panelsContent );
			} else {
				$panelsContent = str_replace( '{{{ control-icon }}}', '<i class="vc_tta-controls-icon" data-vc-tta-controls-icon="' . $atts['c_icon'] . '"></i>', $panelsContent );
			}
		} else {
			$panelsContent = str_replace( '{{{ control-icon }}}', '', $panelsContent );
		}

		return $panelsContent;
	}

	/**
	 * Get optional param shape class.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string|null
	 */
	public function getParamShape( $atts, $content ) {
		if ( isset( $atts['shape'] ) && strlen( $atts['shape'] ) > 0 ) {
			return 'vc_tta-shape-' . $atts['shape'];
		}

		return null;
	}

	/**
	 * Get optional param spacing class.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function getParamSpacing( $atts, $content ) {
		if ( isset( $atts['spacing'] ) && strlen( $atts['spacing'] ) > 0 ) {
			if ( $this->isUnitValueCustom( $atts['spacing'] ) ) {
				return 'vc_tta-spacing-custom';
			}

			return 'vc_tta-spacing-' . $atts['spacing'];
		}

		// In case if no spacing set we need to append extra class.
		return 'vc_tta-o-shape-group';
	}

	/**
	 * Get optional param gap class.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string|null
	 */
	public function getParamGap( $atts, $content ) {
		if ( isset( $atts['gap'] ) && strlen( $atts['gap'] ) > 0 ) {
			// Extract numeric value to check if it's 0.
			$numeric_value = floatval( $atts['gap'] );

			// When gap is explicitly 0 (even with units like "0px"), use vc_tta-gap-0.
			if ( 0.0 === $numeric_value ) {
				return 'vc_tta-gap-0';
			}

			if ( $this->isUnitValueCustom( $atts['gap'] ) ) {
				return 'vc_tta-gap-custom';
			}

			return 'vc_tta-gap-' . $atts['gap'];
		}

		return null;
	}

	/**
	 * Check whether a numeric param value carries a CSS unit suffix (px, em, %, etc.).
	 *
	 * Legacy values were bare integers matching predefined LESS classes
	 * (`vc_tta-spacing-2`). New values from the unit-aware number field are
	 * suffixed (e.g. `2px`, `1.5em`) and are applied through CSS variables.
	 *
	 * @param string $value
	 * @return bool
	 * @since 9.0
	 */
	protected function isUnitValueCustom( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return false;
		}
		return (bool) preg_match( '/^-?\d*\.?\d+[a-zA-Z%]+$/', $value );
	}

	/**
	 * Get optional param no fill class.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string|null
	 */
	public function getParamNoFill( $atts, $content ) {
		$fill = isset( $atts['fill_content_area'] )
			&& ! empty( $atts['fill_content_area'] )
			&& 'false' !== $atts['fill_content_area'];

		if ( ! $fill ) {
			return 'vc_tta-o-no-fill';
		}

		return null;
	}

	/**
	 * Get optional param align class.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string|null
	 */
	public function getParamCAlign( $atts, $content ) {
		if ( isset( $atts['c_align'] ) && strlen( $atts['c_align'] ) > 0 ) {
			return 'vc_tta-controls-align-' . $atts['c_align'];
		}

		return null;
	}

	/**
	 * Accordion doesn't have pagination
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return null
	 */
	public function getParamPaginationTop( $atts, $content ) {
		return null;
	}

	/**
	 * Accordion doesn't have pagination
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return null
	 */
	public function getParamPaginationBottom( $atts, $content ) {
		return null;
	}

	/**
	 * Get currently active section (from $atts)
	 *
	 * @param array $atts
	 * @param bool $strict_bounds If true, check for min/max bounds.
	 *
	 * @return int nth position (one-based) of active section
	 */
	public function getActiveSection( $atts, $strict_bounds = false ) {
		$active_section = intval( $atts['active_section'] );

		if ( $strict_bounds ) {
			VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Section' );
			if ( $active_section < 1 ) {
				$active_section = 1;
			} elseif ( $active_section > WPBakeryShortCode_Vc_Tta_Section::$self_count ) {
				$active_section = WPBakeryShortCode_Vc_Tta_Section::$self_count;
			}
		}

		return $active_section;
	}

	/**
	 * Get pagination list html.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function getParamPaginationList( $atts, $content ) {
		if ( empty( $atts['pagination_style'] ) ) {
			return null;
		}

		$html = [];
		$html[] = vc_get_template( 'partials/tta-pagination-start.php', [
			'classes' => $this->getTtaPaginationClasses(),
			'style' => $this->getTtaPaginationStyle(),
		] );

		if ( ! vc_is_page_editable() ) {
			VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Section' );
			foreach ( WPBakeryShortCode_Vc_Tta_Section::$section_info as $nth => $section ) {
				$active_section = $this->getActiveSection( $atts );

				$classes = [ 'vc_pagination-item' ];
				$current = $nth + 1;
				if ( $current === $active_section ) {
					$classes[] = $this->activeClass;
				}

				$html[] = vc_get_template( 'partials/tta-pagination-item.php', [
					'classes' => implode( ' ', $classes ),
					'section' => $section,
					'current' => $current,
				] );
			}
		}

		$html[] = vc_get_template( 'partials/tta-pagination-end.php' );

		return implode( '', $html );
	}

	/**
	 * Enqueue element specific styles.
	 */
	public function enqueueTtaStyles() {
		wp_register_style( 'vc_tta_style', vc_asset_url( 'css/js_composer_tta.min.css' ), false, WPB_VC_VERSION );
		wp_enqueue_style( 'vc_tta_style' );
	}

	/**
	 * Enqueue element specific scripts.
	 */
	public function enqueueTtaScript() {
		wp_register_script( 'vc_accordion_script', vc_asset_url( 'lib/vc/vc_accordion/vc-accordion.min.js' ), [ 'jquery-core' ], WPB_VC_VERSION, true );
		wp_register_script( 'vc_tta_autoplay_script', vc_asset_url( 'lib/vc/vc-tta-autoplay/vc-tta-autoplay.min.js' ), [ 'vc_accordion_script' ], WPB_VC_VERSION, true );

		wp_enqueue_script( 'vc_accordion_script' );
		if ( ! vc_is_page_editable() ) {
			wp_enqueue_script( 'vc_tta_autoplay_script' );
		}
	}

	/**
	 * Override default outputTitle (also Icon). To remove anything, also Icon.
	 *
	 * @param string $title - just for strict standards.
	 *
	 * @return string
	 */
	protected function outputTitle( $title ) {
		return '';
	}

	/**
	 * Check is allowed to add another element inside current element.
	 *
	 * @return bool
	 * @throws \Exception
	 * @since 4.8
	 */
	public function getAddAllowed() {
		return vc_user_access_check_shortcode_all( 'vc_tta_section' );
	}

	/**
	 * Get CSS file names for vc_tta_accordion shortcode.
	 *
	 * @since 9.0
	 * @return array
	 */
	public function get_shortcode_css_files() {
		return [ 'vc_tta', 'vc_tta_toggle' ];
	}
}
