<?php
/**
 * WPBakery Page Builder Content elements refresh.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class VcSharedLibrary
 *
 * Here we will store plugin wise (shared) settings. Colors, Locations, Sizes, etc.
 */
class VcSharedLibrary {
	/**
	 * Available color options.
	 *
	 * @var array
	 */
	private static $colors = [
		'Blue' => 'blue',
		'Turquoise' => 'turquoise',
		'Pink' => 'pink',
		'Violet' => 'violet',
		'Peacoc' => 'peacoc',
		'Chino' => 'chino',
		'Mulled Wine' => 'mulled_wine',
		'Vista Blue' => 'vista_blue',
		'Black' => 'black',
		'Grey' => 'grey',
		'Orange' => 'orange',
		'Sky' => 'sky',
		'Green' => 'green',
		'Juicy pink' => 'juicy_pink',
		'Sandy brown' => 'sandy_brown',
		'Purple' => 'purple',
		'White' => 'white',
	];

	/**
	 * Button modern/classic/flat style color map: slug => [ background, text, hover-background, hover-text ].
	 *
	 * @var array
	 */
	private static $btn_solid_colors = [
		'blue'        => [ '#5472D2', '#fff', '#3c5ecc', '#f7f7f7' ],
		'turquoise'   => [ '#00C1CF', '#fff', '#00a4b0', '#f7f7f7' ],
		'pink'        => [ '#FE6C61', '#fff', '#fe5043', '#f7f7f7' ],
		'violet'      => [ '#8D6DC4', '#fff', '#7c57bb', '#f7f7f7' ],
		'peacoc'      => [ '#4CADC9', '#fff', '#39a0bd', '#f7f7f7' ],
		'chino'       => [ '#CEC2AB', '#fff', '#c3b498', '#f7f7f7' ],
		'mulled-wine' => [ '#50485B', '#fff', '#413a4a', '#f7f7f7' ],
		'vista-blue'  => [ '#75D69C', '#fff', '#5dcf8b', '#f7f7f7' ],
		'black'       => [ '#2A2A2A', '#fff', '#1b1b1b', '#f7f7f7' ],
		'grey'        => [ '#EBEBEB', '#666', '#dcdcdc', '#5e5e5e' ],
		'orange'      => [ '#F7BE68', '#fff', '#f5b14b', '#f7f7f7' ],
		'sky'         => [ '#5AA1E3', '#fff', '#4092df', '#f7f7f7' ],
		'green'       => [ '#6DAB3C', '#fff', '#5f9434', '#f7f7f7' ],
		'juicy-pink'  => [ '#F4524D', '#fff', '#f23630', '#f7f7f7' ],
		'sandy-brown' => [ '#F79468', '#fff', '#f57f4b', '#f7f7f7' ],
		'purple'      => [ '#B97EBB', '#fff', '#ae6ab0', '#f7f7f7' ],
		'white'       => [ '#FFFFFF', '#666', '#f0f0f0', '#5e5e5e' ],
		// Classic colors (Bootstrap-2 derived WPBakery defaults from variables_common.less).
		'default'     => [ '#f7f7f7', '#333', '#e8e8e8', '#2b2b2b' ],
		'primary'     => [ '#0088cc', '#fff', '#0074ad', '#f7f7f7' ],
		'info'        => [ '#58B9DA', '#fff', '#3fafd4', '#f7f7f7' ],
		'success'     => [ '#6AB165', '#fff', '#59a453', '#f7f7f7' ],
		'warning'     => [ '#FF9900', '#fff', '#e08700', '#f7f7f7' ],
		'danger'      => [ '#FF675B', '#fff', '#ff4b3c', '#f7f7f7' ],
		'inverse'     => [ '#555555', '#fff', '#464646', '#f7f7f7' ],
	];

	/**
	 * Button outline style color map: slug => [ text, border, hover-text ].
	 *
	 * @var array
	 */
	private static $btn_outline_colors = [
		'blue'        => [ '#5472D2', '#5472D2', '#fff' ],
		'turquoise'   => [ '#00C1CF', '#00C1CF', '#fff' ],
		'pink'        => [ '#FE6C61', '#FE6C61', '#fff' ],
		'violet'      => [ '#8D6DC4', '#8D6DC4', '#fff' ],
		'peacoc'      => [ '#4CADC9', '#4CADC9', '#fff' ],
		'chino'       => [ '#CEC2AB', '#CEC2AB', '#fff' ],
		'mulled-wine' => [ '#50485B', '#50485B', '#fff' ],
		'vista-blue'  => [ '#75D69C', '#75D69C', '#fff' ],
		'black'       => [ '#2A2A2A', '#2A2A2A', '#fff' ],
		'grey'        => [ '#EBEBEB', '#EBEBEB', '#666' ],
		'orange'      => [ '#F7BE68', '#F7BE68', '#fff' ],
		'sky'         => [ '#5AA1E3', '#5AA1E3', '#fff' ],
		'green'       => [ '#6DAB3C', '#6DAB3C', '#fff' ],
		'juicy-pink'  => [ '#F4524D', '#F4524D', '#fff' ],
		'sandy-brown' => [ '#F79468', '#F79468', '#fff' ],
		'purple'      => [ '#B97EBB', '#B97EBB', '#fff' ],
		'white'       => [ '#FFFFFF', '#FFFFFF', '#666' ],
		'default'     => [ '#f7f7f7', '#f7f7f7', '#333' ],
		'primary'     => [ '#0088cc', '#0088cc', '#fff' ],
		'info'        => [ '#58B9DA', '#58B9DA', '#fff' ],
		'success'     => [ '#6AB165', '#6AB165', '#fff' ],
		'warning'     => [ '#FF9900', '#FF9900', '#fff' ],
		'danger'      => [ '#FF675B', '#FF675B', '#fff' ],
		'inverse'     => [ '#555555', '#555555', '#fff' ],
	];

	/**
	 * Button 3d style color map: slug => [ background, text, shadow ].
	 *
	 * Shadow colors are darken(@background, 11%) values taken from the compiled CSS.
	 *
	 * @var array
	 */
	private static $btn_3d_colors = [
		'blue'        => [ '#5472D2', '#fff', '#3253bc' ],
		'turquoise'   => [ '#00C1CF', '#fff', '#008d97' ],
		'pink'        => [ '#FE6C61', '#fff', '#fe3829' ],
		'violet'      => [ '#8D6DC4', '#fff', '#6e48b1' ],
		'peacoc'      => [ '#4CADC9', '#fff', '#338faa' ],
		'chino'       => [ '#CEC2AB', '#fff', '#b9a888' ],
		'mulled-wine' => [ '#50485B', '#fff', '#342f3c' ],
		'vista-blue'  => [ '#75D69C', '#fff', '#4ac97d' ],
		'black'       => [ '#2A2A2A', '#fff', '#0e0e0e' ],
		'grey'        => [ '#EBEBEB', '#666', '#cfcfcf' ],
		'orange'      => [ '#F7BE68', '#fff', '#f4a733' ],
		'sky'         => [ '#5AA1E3', '#fff', '#2a86db' ],
		'green'       => [ '#6DAB3C', '#fff', '#53812d' ],
		'juicy-pink'  => [ '#F4524D', '#fff', '#f11f18' ],
		'sandy-brown' => [ '#F79468', '#fff', '#f46e33' ],
		'purple'      => [ '#B97EBB', '#fff', '#a559a8' ],
		'white'       => [ '#FFFFFF', '#666', '#e3e3e3' ],
		'default'     => [ '#f7f7f7', '#333', '#dbdbdb' ],
		'primary'     => [ '#0088cc', '#fff', '#006394' ],
		'info'        => [ '#58B9DA', '#fff', '#2da4cd' ],
		'success'     => [ '#6AB165', '#fff', '#4f934b' ],
		'warning'     => [ '#FF9900', '#fff', '#c77700' ],
		'danger'      => [ '#FF675B', '#fff', '#ff3323' ],
		'inverse'     => [ '#555555', '#fff', '#393939' ],
	];

	/**
	 * CTA style color map: dashed-slug => [ text, background, heading, shadow ].
	 *
	 * Text/background/heading: @vc_cta3-color-{slug}-text, @vc_cta3-color-{slug}, @vc_cta3-color-{slug}-headings.
	 * Shadow: darken(@vc_cta3-color-{slug}, 11%) — used for 3d box-shadow.
	 *
	 * @var array
	 */
	private static $cta_colors = [
		'classic'     => [ '#9d9d9e', '#f0f0f0', '#666', '#d4d4d4' ],
		'blue'        => [ '#c9d2f0', '#5472d2', '#fff', '#3253bc' ],
		'turquoise'   => [ '#d3f5f1', '#00c1cf', '#fff', '#008d97' ],
		'pink'        => [ '#fcdbd7', '#fe6c61', '#fff', '#fe3829' ],
		'violet'      => [ '#e1d5f5', '#8d6dc4', '#fff', '#6e48b1' ],
		'peacoc'      => [ '#d0edf5', '#4cadc9', '#fff', '#338faa' ],
		'chino'       => [ '#f7f3eb', '#cec2ab', '#fff', '#b9a888' ],
		'mulled-wine' => [ '#e2ddeb', '#50485b', '#fff', '#342f3c' ],
		'vista-blue'  => [ '#e1f5e9', '#75d69c', '#fff', '#4ac97d' ],
		'black'       => [ '#d9d9d9', '#2a2a2a', '#fff', '#0e0e0e' ],
		'grey'        => [ '#9d9d9e', '#ebebeb', '#666', '#cfcfcf' ],
		'orange'      => [ '#faf0e1', '#f7be68', '#fff', '#f4a733' ],
		'sky'         => [ '#dce9f5', '#5aa1e3', '#fff', '#2a86db' ],
		'green'       => [ '#e5f2da', '#6dab3c', '#fff', '#53812d' ],
		'juicy-pink'  => [ '#fce2e1', '#f4524d', '#fff', '#f11f18' ],
		'sandy-brown' => [ '#f7e1d7', '#f79468', '#fff', '#f46e33' ],
		'purple'      => [ '#f4dff5', '#b97ebb', '#fff', '#a559a8' ],
		'white'       => [ '#9d9d9e', '#ffffff', '#666', '#e3e3e3' ],
	];

	/**
	 * Hex color values keyed by dashed color slugs.
	 *
	 * @var array
	 */
	private static $dashed_color_hash = [
		'blue'        => '#5472d2',
		'turquoise'   => '#00c1cf',
		'pink'        => '#fe6c61',
		'violet'      => '#8d6dc4',
		'peacoc'      => '#4cadc9',
		'chino'       => '#cec2ab',
		'mulled-wine' => '#50485b',
		'vista-blue'  => '#75d69c',
		'orange'      => '#f7be68',
		'sky'         => '#5aa1e3',
		'green'       => '#6dab3c',
		'juicy-pink'  => '#f4524d',
		'sandy-brown' => '#f79468',
		'purple'      => '#b97ebb',
		'black'       => '#2a2a2a',
		'grey'        => '#ebebeb',
		'white'       => '#ffffff',
		'default'     => '#f7f7f7',
		'primary'     => '#0088cc',
		'info'        => '#58b9da',
		'success'     => '#6ab165',
		'warning'     => '#ff9900',
		'danger'      => '#ff675b',
		'inverse'     => '#555555',
	];

	/**
	 * Hex color values keyed by shared color slugs (underscore variants).
	 *
	 * @var array
	 */
	private static $colors_hash = [
		'blue' => '#5472D2',
		'turquoise' => '#00C1CF',
		'pink' => '#FE6C61',
		'violet' => '#8D6DC4',
		'peacoc' => '#4CADC9',
		'chino' => '#CEC2AB',
		'mulled_wine' => '#50485B',
		'vista_blue' => '#75D69C',
		'black' => '#2A2A2A',
		'grey' => '#EBEBEB',
		'orange' => '#F7BE68',
		'sky' => '#5AA1E3',
		'green' => '#6DAB3C',
		'juicy_pink' => '#F4524D',
		'sandy_brown' => '#F79468',
		'purple' => '#B97EBB',
		'white' => '#FFFFFF',
	];

	/**
	 * Available icon options.
	 *
	 * @var array
	 */
	public static $icons = [
		'Glass' => 'glass',
		'Music' => 'music',
		'Search' => 'search',
	];

	/**
	 * Available size options.
	 *
	 * @var array
	 */
	public static $sizes = [
		'Mini' => 'xs',
		'Small' => 'sm',
		'Normal' => 'md',
		'Large' => 'lg',
	];

	/**
	 * Available button styles.
	 *
	 * @var array
	 */
	public static $button_styles = [
		'Rounded' => 'rounded',
		'Square' => 'square',
		'Round' => 'round',
		'Outlined' => 'outlined',
		'3D' => '3d',
		'Square Outlined' => 'square_outlined',
	];

	/**
	 * Settings navigation icons.
	 * Maps page slugs to their icon template paths.
	 *
	 * @var array
	 */
	public static $settings_nav_icons = [
		'vc-general' => '/icons/settings-ico.tpl.php',
		'vc-modules' => '/icons/settings-ico.tpl.php',
		'vc-updater' => '/icons/license-ico.tpl.php',
		'vc-color' => '/icons/design-settings-ico.tpl.php',
		'vc-color-picker' => '/icons/design-settings-ico.tpl.php',
		'vc-roles' => '/icons/role-manager-ico.tpl.php',
		'vc-custom_css' => '/icons/custom-code-ico.tpl.php',
		'vc-custom_js' => '/icons/custom-code-ico.tpl.php',
		'vc-ai' => '/icons/wpb-ai-ico.tpl.php',
		'vc-automapper' => '/icons/shortcode-mapper-ico.tpl.php',
		'vc-typography' => '/icons/typography-ico.tpl.php',
		'templatera' => '/icons/global-templates-ico.tpl.php',
		'vc-welcome' => '/icons/wpbakery-ico.tpl.php',
	];

	/**
	 * Available message box styles.
	 *
	 * @var array
	 */
	public static $message_box_styles = [
		'Standard' => 'standard',
		'Solid' => 'solid',
		'Solid icon' => 'solid-icon',
		'Outline' => 'outline',
		'3D' => '3d',
	];

	/**
	 * Available toggle styles.
	 *
	 * @var array
	 */
	public static $toggle_styles = [
		'Default' => 'default',
		'Simple' => 'simple',
		'Round' => 'round',
		'Round Outline' => 'round_outline',
		'Rounded' => 'rounded',
		'Rounded Outline' => 'rounded_outline',
		'Square' => 'square',
		'Square Outline' => 'square_outline',
		'Arrow' => 'arrow',
		'Text Only' => 'text_only',
	];

	/**
	 * Available animation styles.
	 *
	 * @var array
	 */
	public static $animation_styles = [
		'Bounce' => 'easeOutBounce',
		'Elastic' => 'easeOutElastic',
		'Back' => 'easeOutBack',
		'Cubic' => 'easeInOutCubic',
		'Quint' => 'easeInOutQuint',
		'Quart' => 'easeOutQuart',
		'Quad' => 'easeInQuad',
		'Sine' => 'easeOutSine',
	];

	/**
	 * Available call to action styles.
	 *
	 * @var array
	 */
	public static $cta_styles = [
		'Rounded' => 'rounded',
		'Square' => 'square',
		'Round' => 'round',
		'Outlined' => 'outlined',
		'Square Outlined' => 'square_outlined',
	];

	/**
	 * Available text align options.
	 *
	 * @var array
	 */
	public static $txt_align = [
		'Left' => 'left',
		'Right' => 'right',
		'Center' => 'center',
		'Justify' => 'justify',
	];

	/**
	 * Available element widths.
	 *
	 * @var array
	 */
	public static $el_widths = [
		'100%' => '',
		'90%' => '90',
		'80%' => '80',
		'70%' => '70',
		'60%' => '60',
		'50%' => '50',
		'40%' => '40',
		'30%' => '30',
		'20%' => '20',
		'10%' => '10',
	];

	/**
	 * Available separator widths.
	 *
	 * @var array
	 */
	public static $sep_widths = [
		'1px' => '',
		'2px' => '2',
		'3px' => '3',
		'4px' => '4',
		'5px' => '5',
		'6px' => '6',
		'7px' => '7',
		'8px' => '8',
		'9px' => '9',
		'10px' => '10',
	];

	/**
	 * Available separator styles.
	 *
	 * @var array
	 */
	public static $sep_styles = [
		'Border' => '',
		'Dashed' => 'dashed',
		'Dotted' => 'dotted',
		'Double' => 'double',
		'Shadow' => 'shadow',
	];

	/**
	 * Available box styles.
	 *
	 * @var array
	 */
	public static $box_styles = [
		'Default' => '',
		'Rounded' => 'vc_box_rounded',
		'Border' => 'vc_box_border',
		'Outline' => 'vc_box_outline',
		'Shadow' => 'vc_box_shadow',
		'Bordered shadow' => 'vc_box_shadow_border',
		'3D Shadow' => 'vc_box_shadow_3d',
	];

	/**
	 * Available round box styles.
	 *
	 * @var array
	 */
	public static $round_box_styles = [
		'Round' => 'vc_box_circle',
		'Round Border' => 'vc_box_border_circle',
		'Round Outline' => 'vc_box_outline_circle',
		'Round Shadow' => 'vc_box_shadow_circle',
		'Round Border Shadow' => 'vc_box_shadow_border_circle',
	];

	/**
	 * Default CSS units for UI pickers (number/linked_fields params).
	 *
	 * @var array
	 */
	private static $default_units = [ 'px', 'em', 'rem', 'vw', 'vh', '%' ];

	/**
	 * Available circle box styles.
	 *
	 * @var array
	 */
	public static $circle_box_styles = [
		'Circle' => 'vc_box_circle_2',
		'Circle Border' => 'vc_box_border_circle_2',
		'Circle Outline' => 'vc_box_outline_circle_2',
		'Circle Shadow' => 'vc_box_shadow_circle_2',
		'Circle Border Shadow' => 'vc_box_shadow_border_circle_2',
	];

	/**
	 * Get available colors.
	 *
	 * @return array
	 */
	public static function getColors() {
		return self::$colors;
	}

	/**
	 * Get available colors hash.
	 *
	 * @since 9.0
	 * @return array
	 */
	public static function getColorsHash() {
		return self::$colors_hash;
	}

	/**
	 * Get hex color values keyed by dashed slugs.
	 *
	 * @return array
	 */
	public static function getDashedColorHash() {
		return self::$dashed_color_hash;
	}

	/**
	 * Get CTA flat style color map.
	 *
	 * @return array
	 */
	public static function getCTAColors() {
		return self::$cta_colors;
	}

	/**
	 * Get button solid style color map (modern, classic, flat).
	 *
	 * @return array
	 */
	public static function getBtnSolidColors() {
		return self::$btn_solid_colors;
	}

	/**
	 * Get button outline style color map.
	 *
	 * @return array
	 */
	public static function getBtnOutlineColors() {
		return self::$btn_outline_colors;
	}

	/**
	 * Get button 3d style color map.
	 *
	 * @return array
	 */
	public static function getBtn3dColors() {
		return self::$btn_3d_colors;
	}

	/**
	 * Get available icons.
	 *
	 * @return array
	 */
	public static function getIcons() {
		return self::$icons;
	}

	/**
	 * Get available sizes.
	 *
	 * @return array
	 */
	public static function getSizes() {
		return self::$sizes;
	}

	/**
	 * Get settings navigation icons.
	 *
	 * @return array
	 */
	public static function getSettingsNavIcons() {
		return self::$settings_nav_icons;
	}

	/**
	 * Get icon template path for a specific settings page.
	 *
	 * @param string $page_slug Page slug (e.g., 'vc-general', 'vc-design', etc.).
	 *
	 * @return string|null Icon template path or null if not found.
	 */
	public static function getSettingsNavIcon( $page_slug ) {
		return self::$settings_nav_icons[ $page_slug ] ?? null;
	}

	/**
	 * Get available button styles.
	 *
	 * @return array
	 */
	public static function getButtonStyles() {
		return self::$button_styles;
	}

	/**
	 * Get available message box styles.
	 *
	 * @return array
	 */
	public static function getMessageBoxStyles() {
		return self::$message_box_styles;
	}

	/**
	 * Get available toggle styles.
	 *
	 * @return array
	 */
	public static function getToggleStyles() {
		return self::$toggle_styles;
	}

	/**
	 * Get available animation styles.
	 *
	 * @return array
	 */
	public static function getAnimationStyles() {
		return self::$animation_styles;
	}

	/**
	 * Get available call to action styles.
	 *
	 * @return array
	 */
	public static function getCtaStyles() {
		return self::$cta_styles;
	}

	/**
	 * Get available text align options.
	 *
	 * @return array
	 */
	public static function getTextAlign() {
		return self::$txt_align;
	}

	/**
	 * Get available element widths.
	 *
	 * @return array
	 */
	public static function getBorderWidths() {
		return self::$sep_widths;
	}

	/**
	 * Get available element widths.
	 *
	 * @return array
	 */
	public static function getElementWidths() {
		return self::$el_widths;
	}

	/**
	 * Get available separator styles.
	 *
	 * @return array
	 */
	public static function getSeparatorStyles() {
		return self::$sep_styles;
	}

	/**
	 * Get list of box styles
	 *
	 * Possible $groups values:
	 * - default
	 * - round
	 * - circle
	 *
	 * @param array $groups Array of groups to include. If not specified, return all.
	 *
	 * @return array
	 */
	public static function getBoxStyles( $groups = [] ) {
		$list = [];
		$groups = (array) $groups;

		if ( ! $groups || in_array( 'default', $groups, true ) ) {
			$list += self::$box_styles;
		}

		if ( ! $groups || in_array( 'round', $groups, true ) ) {
			$list += self::$round_box_styles;
		}

		if ( ! $groups || in_array( 'cirlce', $groups, true ) ) {
			$list += self::$circle_box_styles;
		}

		return $list;
	}

	/**
	 * Get available colors.
	 *
	 * @return array
	 */
	public static function getColorsDashed() {
		$colors = [
			esc_html__( 'Blue', 'js_composer' ) => 'blue',
			esc_html__( 'Turquoise', 'js_composer' ) => 'turquoise',
			esc_html__( 'Pink', 'js_composer' ) => 'pink',
			esc_html__( 'Violet', 'js_composer' ) => 'violet',
			esc_html__( 'Peacoc', 'js_composer' ) => 'peacoc',
			esc_html__( 'Chino', 'js_composer' ) => 'chino',
			esc_html__( 'Mulled Wine', 'js_composer' ) => 'mulled-wine',
			esc_html__( 'Vista Blue', 'js_composer' ) => 'vista-blue',
			esc_html__( 'Black', 'js_composer' ) => 'black',
			esc_html__( 'Grey', 'js_composer' ) => 'grey',
			esc_html__( 'Orange', 'js_composer' ) => 'orange',
			esc_html__( 'Sky', 'js_composer' ) => 'sky',
			esc_html__( 'Green', 'js_composer' ) => 'green',
			esc_html__( 'Juicy pink', 'js_composer' ) => 'juicy-pink',
			esc_html__( 'Sandy brown', 'js_composer' ) => 'sandy-brown',
			esc_html__( 'Purple', 'js_composer' ) => 'purple',
			esc_html__( 'White', 'js_composer' ) => 'white',
		];

		return $colors;
	}

	/**
	 * Get available icon libraries.
	 *
	 * @return array
	 */
	public static function getIconLibraries() {
		$icon_libraries = [
			esc_html__( 'Font Awesome', 'js_composer' ) => 'fontawesome',
			esc_html__( 'Open Iconic', 'js_composer' ) => 'openiconic',
			esc_html__( 'Typicons', 'js_composer' ) => 'typicons',
			esc_html__( 'Entypo', 'js_composer' ) => 'entypo',
			esc_html__( 'Linecons', 'js_composer' ) => 'linecons',
			esc_html__( 'Mono Social', 'js_composer' ) => 'monosocial',
			esc_html__( 'Material', 'js_composer' ) => 'material',
			esc_html__( 'Pixel', 'js_composer' ) => 'pixelicons',
		];

		return $icon_libraries;
	}

	/**
	 * Get configuration for pixel_icons shortcode.
	 *
	 * @since 8.6
	 * @return array
	 */
	public static function get_pixel_icons() {
		return [
			[ 'vc_pixel_icon vc_pixel_icon-alert' => esc_html__( 'Alert', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-info' => esc_html__( 'Info', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-tick' => esc_html__( 'Tick', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-explanation' => esc_html__( 'Explanation', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-address_book' => esc_html__( 'Address book', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-alarm_clock' => esc_html__( 'Alarm clock', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-anchor' => esc_html__( 'Anchor', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-application_image' => esc_html__( 'Application Image', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-arrow' => esc_html__( 'Arrow', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-asterisk' => esc_html__( 'Asterisk', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-hammer' => esc_html__( 'Hammer', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-balloon' => esc_html__( 'Balloon', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-balloon_buzz' => esc_html__( 'Balloon Buzz', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-balloon_facebook' => esc_html__( 'Balloon Facebook', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-balloon_twitter' => esc_html__( 'Balloon Twitter', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-battery' => esc_html__( 'Battery', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-binocular' => esc_html__( 'Binocular', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-document_excel' => esc_html__( 'Document Excel', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-document_image' => esc_html__( 'Document Image', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-document_music' => esc_html__( 'Document Music', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-document_office' => esc_html__( 'Document Office', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-document_pdf' => esc_html__( 'Document PDF', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-document_powerpoint' => esc_html__( 'Document Powerpoint', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-document_word' => esc_html__( 'Document Word', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-bookmark' => esc_html__( 'Bookmark', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-camcorder' => esc_html__( 'Camcorder', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-camera' => esc_html__( 'Camera', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-chart' => esc_html__( 'Chart', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-chart_pie' => esc_html__( 'Chart pie', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-clock' => esc_html__( 'Clock', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-fire' => esc_html__( 'Fire', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-heart' => esc_html__( 'Heart', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-mail' => esc_html__( 'Mail', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-play' => esc_html__( 'Play', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-shield' => esc_html__( 'Shield', 'js_composer' ) ],
			[ 'vc_pixel_icon vc_pixel_icon-video' => esc_html__( 'Video', 'js_composer' ) ],
		];
	}
	/**
	 * Get configuration for icons attribute.
	 *
	 * @since 8.6
	 * @return array
	 */
	public static function get_icons_arr() {
		return [
			esc_html__( 'None', 'js_composer' ) => 'none',
			esc_html__( 'Address book icon', 'js_composer' ) => 'wpb_address_book',
			esc_html__( 'Alarm clock icon', 'js_composer' ) => 'wpb_alarm_clock',
			esc_html__( 'Anchor icon', 'js_composer' ) => 'wpb_anchor',
			esc_html__( 'Application Image icon', 'js_composer' ) => 'wpb_application_image',
			esc_html__( 'Arrow icon', 'js_composer' ) => 'wpb_arrow',
			esc_html__( 'Asterisk icon', 'js_composer' ) => 'wpb_asterisk',
			esc_html__( 'Hammer icon', 'js_composer' ) => 'wpb_hammer',
			esc_html__( 'Balloon icon', 'js_composer' ) => 'wpb_balloon',
			esc_html__( 'Balloon Buzz icon', 'js_composer' ) => 'wpb_balloon_buzz',
			esc_html__( 'Balloon Facebook icon', 'js_composer' ) => 'wpb_balloon_facebook',
			esc_html__( 'Balloon Twitter icon', 'js_composer' ) => 'wpb_balloon_twitter',
			esc_html__( 'Battery icon', 'js_composer' ) => 'wpb_battery',
			esc_html__( 'Binocular icon', 'js_composer' ) => 'wpb_binocular',
			esc_html__( 'Document Excel icon', 'js_composer' ) => 'wpb_document_excel',
			esc_html__( 'Document Image icon', 'js_composer' ) => 'wpb_document_image',
			esc_html__( 'Document Music icon', 'js_composer' ) => 'wpb_document_music',
			esc_html__( 'Document Office icon', 'js_composer' ) => 'wpb_document_office',
			esc_html__( 'Document PDF icon', 'js_composer' ) => 'wpb_document_pdf',
			esc_html__( 'Document Powerpoint icon', 'js_composer' ) => 'wpb_document_powerpoint',
			esc_html__( 'Document Word icon', 'js_composer' ) => 'wpb_document_word',
			esc_html__( 'Bookmark icon', 'js_composer' ) => 'wpb_bookmark',
			esc_html__( 'Camcorder icon', 'js_composer' ) => 'wpb_camcorder',
			esc_html__( 'Camera icon', 'js_composer' ) => 'wpb_camera',
			esc_html__( 'Chart icon', 'js_composer' ) => 'wpb_chart',
			esc_html__( 'Chart pie icon', 'js_composer' ) => 'wpb_chart_pie',
			esc_html__( 'Clock icon', 'js_composer' ) => 'wpb_clock',
			esc_html__( 'Fire icon', 'js_composer' ) => 'wpb_fire',
			esc_html__( 'Heart icon', 'js_composer' ) => 'wpb_heart',
			esc_html__( 'Mail icon', 'js_composer' ) => 'wpb_mail',
			esc_html__( 'Play icon', 'js_composer' ) => 'wpb_play',
			esc_html__( 'Shield icon', 'js_composer' ) => 'wpb_shield',
			esc_html__( 'Video icon', 'js_composer' ) => 'wpb_video',
		];
	}

	/**
	 * Get configuration for sizes attribute.
	 *
	 * @since 8.6
	 * @return array
	 */
	public static function get_sizes_arr() {
		return [
			esc_html__( 'Regular', 'js_composer' ) => 'wpb_regularsize',
			esc_html__( 'Large', 'js_composer' ) => 'btn-large',
			esc_html__( 'Small', 'js_composer' ) => 'btn-small',
			esc_html__( 'Mini', 'js_composer' ) => 'btn-mini',
		];
	}

	/**
	 * Get configuration for colors attribute.
	 *
	 * @since 8.6
	 * @return array
	 */
	public static function get_color_arr() {
		return [
			esc_html__( 'Grey', 'js_composer' ) => 'wpb_button',
			esc_html__( 'Blue', 'js_composer' ) => 'btn-primary',
			esc_html__( 'Turquoise', 'js_composer' ) => 'btn-info',
			esc_html__( 'Green', 'js_composer' ) => 'btn-success',
			esc_html__( 'Orange', 'js_composer' ) => 'btn-warning',
			esc_html__( 'Red', 'js_composer' ) => 'btn-danger',
			esc_html__( 'Black', 'js_composer' ) => 'btn-inverse',
		];
	}

	/**
	 * Get configuration for target controls.
	 *
	 * @since 8.6
	 * @return array
	 */
	public static function get_target_param_list() {
		return [
			esc_html__( 'Same window', 'js_composer' ) => '_self',
			esc_html__( 'New window', 'js_composer' ) => '_blank',
		];
	}

	/**
	 * Get configuration for hotkey controls.
	 *
	 * @since 8.6
	 * @return array
	 */
	public static function get_shortcut_list() {
		return [
			__( 'Undo', 'js_composer' ) => '%s+Z',
			__( 'Redo', 'js_composer' ) => '%s+Shift+Z',
			__( 'Templates', 'js_composer' ) => 'Shift+T',
			__( 'Add new element', 'js_composer' ) => 'Shift+A',
			__( 'Close', 'js_composer' ) => 'ESC',
			__( 'Preview', 'js_composer' ) => '%s+Shift+P',
			__( 'Save draft', 'js_composer' ) => '%s+Shift+S',
			__( 'Save changes', 'js_composer' ) => '%s+Shift+S',
			__( 'Save as Pending', 'js_composer' ) => '%s+Shift+S',
			__( 'Submit for Review', 'js_composer' ) => '%s+Shift+S',
			__( 'Update', 'js_composer' ) => '%s+Shift+S',
			__( 'Publish', 'js_composer' ) => '%s+Shift+S',
			__( 'WPBakery SEO', 'js_composer' ) => 'Shift+I',
			__( 'Exit WPBakery Page Builder edit mode', 'js_composer' ) => '%s+Shift+V',
			__( 'Custom CSS/JS', 'js_composer' ) => 'Shift+C',
			/* translators: %s: post type noun, e.g. "Post" or "Page". */
			sprintf( __( '%s settings', 'js_composer' ), wpb_get_post_type_noun() ) => 'Shift+S',
		];
	}

	/**
	 * Get configuration for responsive controls.
	 *
	 * @since 9.0
	 * @return array
	 */
	public static function get_screen_sizes() {
		return [
			[
				'title' => esc_html__( 'Desktop', 'js_composer' ),
				'size' => '100%',
				'key' => 'default',
				'active' => true,
			],
			[
				'title' => esc_html__( 'Tablet landscape mode', 'js_composer' ),
				'size' => '1024px',
				'key' => 'landscape-tablets',
			],
			[
				'title' => esc_html__( 'Tablet portrait mode', 'js_composer' ),
				'size' => '768px',
				'key' => 'portrait-tablets',
			],
			[
				'title' => esc_html__( 'Smartphone landscape mode', 'js_composer' ),
				'size' => '640px',
				'key' => 'landscape-smartphones',
			],
			[
				'title' => esc_html__( 'Smartphone portrait mode', 'js_composer' ),
				'size' => '480px',
				'key' => 'portrait-smartphones',
			],
		];
	}

	/**
	 * Get default CSS units for UI pickers.
	 *
	 * @since 9.0
	 * @return array
	 */
	public static function getDefaultUnits() {
		return self::$default_units;
	}

	/**
	 * Get CSS supported units.
	 *
	 * @since 9.0
	 * @return array
	 */
	public static function get_css_units() {
		return [
			'px',
			'%',
			'in',
			'cm',
			'mm',
			'em',
			'rem',
			'ex',
			'pt',
			'pc',
			'vw',
			'vh',
			'vmin',
			'vmax',
		];
	}
}
