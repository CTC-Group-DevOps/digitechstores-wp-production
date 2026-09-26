<?php
/**
 * WPBakery Page Builder deprecated helpers functions.
 *
 * Functions of here are deprecated and will be removed in future releases.
 *
 * @deprecated
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! function_exists( 'add_shortcode_param' ) ) :
	/**
	 * Helper function to register new shortcode attribute hook.
	 *
	 * @param string $name - attribute name.
	 * @param callable $form_field_callback - hook, will be called when settings form is shown and attribute added to shortcode param list.
	 * @param string $script_url - javascript file url which will be attached at the end of settings form.
	 *
	 * @return bool
	 * @deprecated due to without prefix name 4.4
	 * @since 4.2
	 */
	function add_shortcode_param( $name, $form_field_callback, $script_url = null ) {
		_deprecated_function( 'add_shortcode_param', '4.4', 'vc_add_shortcode_param' );

		return vc_add_shortcode_param( $name, $form_field_callback, $script_url );
	}
endif;
if ( ! function_exists( 'get_row_css_class' ) ) :
	/**
	 * Get row css class.
	 *
	 * @return mixed|string
	 * @since 4.2
	 * @deprecated 4.2
	 */
	function get_row_css_class() {
		_deprecated_function( 'get_row_css_class', '4.2' );
		$custom = vc_settings()->get( 'row_css_class' );

		return ! empty( $custom ) ? $custom : 'vc_row-fluid';
	}
endif;
if ( ! function_exists( 'vc_generate_dependencies_attributes' ) ) :
	/**
	 * Generate dependencies attributes for shortcode.
	 *
	 * @return string
	 * @deprecated 5.2
	 */
	function vc_generate_dependencies_attributes() {
		_deprecated_function( 'vc_generate_dependencies_attributes', '5.1' );

		return '';
	}
endif;
if ( ! function_exists( 'vcExtractDimensions' ) ) :
	/**
	 * Extract width/height from string
	 *
	 * @param string $dimensions WxH.
	 * @return mixed array(width, height) or false.
	 * @since 4.7
	 *
	 * @deprecated since 5.8
	 */
    function vcExtractDimensions( $dimensions ) { // phpcs:ignore
		_deprecated_function( 'vcExtractDimensions', '5.8', 'vc_extract_dimensions' );

		return vc_extract_dimensions( $dimensions );
	}
endif;
if ( ! function_exists( 'fieldAttachedImages' ) ) :
	/**
	 * Get image by attachment id.
	 *
	 * @param array $images IDs or srcs of images.
	 * @return string
	 * @since 4.2
	 * @deprecated since 2019, 5.8
	 */
    function fieldAttachedImages( $images = array() ) { // phpcs:ignore
		_deprecated_function( 'fieldAttachedImages', '5.8', 'vc_field_attached_images' );

		return vc_field_attached_images( $images );
	}
endif;
if ( ! function_exists( 'getVcShared' ) ) :
	/**
	 * Get shared asset.
	 *
	 * @param string $asset
	 *
	 * @return array|string
	 * @deprecated
	 */
    function getVcShared( $asset = '' ) { // phpcs:ignore
		_deprecated_function( 'getVcShared', '5.8', 'vc_get_shared' );

		return vc_get_shared( $asset );
	}
endif;
if ( ! function_exists( 'vc_wp_action' ) ) :
	/**
	 * Return a action param for ajax
	 *
	 * @return bool
	 * @since 4.8
	 * @deprecated 6.1
	 */
	function vc_wp_action() {
		_deprecated_function( 'vc_wp_action', '6.1', 'vc_request_param' );

		return vc_request_param( 'action' );
	}
endif;
if ( ! function_exists( 'set_vc_is_inline' ) ) :
	/**
	 * Set inline mode.
	 *
	 * @param bool $value
	 *
	 * @deprecated 5.2
	 * @since 4.3
	 */
	function set_vc_is_inline( $value = true ) {
		_deprecated_function( 'set_vc_is_inline', '5.2' );
		global $vc_is_inline;
		$vc_is_inline = $value;
	}
endif;
if ( ! function_exists( 'vc_is_editor' ) ) :
	/**
	 * Check is plugin editor;
	 *
	 * @deprecated since 4.8 ( use vc_is_frontend_editor ).
	 * @return bool
	 * @since 4.2
	 */
	function vc_is_editor() {
		_deprecated_function( 'vc_is_editor', '4.8', 'vc_is_frontend_editor' );
		return vc_is_frontend_editor();
	}
endif;
if ( ! function_exists( 'vc_disable_automapper' ) ) :
	/**
	 * Disable automapper.
	 *
	 * @deprecated 7.7 ( use modules settings )
	 * @param bool $disable
	 * @since 4.2
	 */
	function vc_disable_automapper( $disable = true ) {
		_deprecated_function( __FUNCTION__, '7.7', 'Use plugin settings module tab to disable automapper' );
		vc_automapper()->setDisabled( $disable );
	}
endif;
if ( ! function_exists( 'vc_automapper_is_disabled' ) ) :
	/**
	 * Check is automapper disabled.
	 *
	 * @deprecated 7.7 ( use modules settings )
	 * @return bool
	 * @since 4.2
	 */
	function vc_automapper_is_disabled() {
		_deprecated_function( __FUNCTION__, '7.7', 'Use plugin settings module tab to disable automapper' );
		return vc_automapper()->disabled();
	}
endif;
if ( ! function_exists( 'visual_composer' ) ) :
	/**
	 * Alias for wpbakery.
	 *
	 * @return Vc_Base
	 * @since 4.2
	 * @deprecated 5.8, use wpbakery() instead
	 */
	function visual_composer() {
		_deprecated_function( __FUNCTION__, '5.8', 'wpbakery' );
		return wpbakery();
	}
endif;
if ( ! function_exists( 'js_composer_body_class' ) ) :
	/**
	 * Method adds css class to body tag.
	 *
	 * Hooked class method by body_class WP filter. Method adds custom css class to body tag of the page to help
	 * identify and build design specially for VC shortcodes.
	 * Used in wp-content/plugins/js_composer/include/classes/core/class-vc-base.php\Vc_Base\bodyClass.
	 *
	 * @param array $classes
	 *
	 * @return array
	 * @since 4.2
	 * @deprecated 8.5
	 */
	function js_composer_body_class( $classes ) {
		_deprecated_function( __FUNCTION__, '8.5', 'wpb_body_class' );
		return wpb_body_class( $classes );
	}
endif;
if ( ! function_exists( 'vc_set_default_content_for_post_type' ) ) :
	/**
	 * Set default content by post type in editor.
	 *
	 * Data for post type templates stored in settings.
	 *
	 * @param string|null $post_content
	 * @param WP_Post $post
	 * @return string|null
	 * @throws Exception
	 * @deprecated 8.5
	 * @since 4.12
	 */
	function vc_set_default_content_for_post_type( $post_content, $post ) {
		_deprecated_function( __FUNCTION__, '8.5', 'vc_set_default_content_for_post_type_back_editor' );
		return vc_set_default_content_for_post_type_back_editor( $post_content, $post );
	}
endif;
if ( ! function_exists( 'vc_add_css_animation' ) ) :
	/**
	 * Add CSS Animation to element.
	 *
	 * @return mixed|void
	 * @deprecated 4.12
	 */
	function vc_add_css_animation() {
			_deprecated_function( __FUNCTION__, '4.12', 'vc_map_add_css_animation' );
			return vc_map_add_css_animation();
	}
endif;
if ( ! function_exists( 'vc_add_css_animation' ) ) :
	/**
	 * Get configuration for post link controls.
	 *
	 * @return array
	 * @deprecated 8.6
	 */
	function vc_layout_sub_controls() {
		_deprecated_function( __FUNCTION__, '8.6' );
		return [
			[
				'link_post',
				esc_html__( 'Link to post', 'js_composer' ),
			],
			[
				'no_link',
				esc_html__( 'No link', 'js_composer' ),
			],
			[
				'link_image',
				esc_html__( 'Link to bigger image', 'js_composer' ),
			],
		];
	}
endif;
if ( ! function_exists( 'vc_pixel_icons' ) ) :
	/**
	 * Get configuration for pixel_icons shortcode.
	 *
	 * @return array
	 * @deprecated 8.6
	 */
	function vc_pixel_icons() {
		_deprecated_function( __FUNCTION__, '8.6', "vc_get_shared( 'pixel icons' )" );
		return vc_get_shared( 'pixel icons' );
	}
endif;
if ( ! function_exists( 'vc_icons_arr' ) ) :
	/**
	 * Get configuration for icons attribute.
	 *
	 * @return array
	 * @deprecated 8.6
	 */
	function vc_icons_arr() {
		_deprecated_function( __FUNCTION__, '8.6', "vc_get_shared( 'icons arr' )" );
		return vc_get_shared( 'icons arr' );
	}
endif;
if ( ! function_exists( 'vc_size_arr' ) ) :
	/**
	 * Get configuration for sizes attribute.
	 *
	 * @return array
	 * @deprecated 8.6
	 */
	function vc_size_arr() {
		_deprecated_function( __FUNCTION__, '8.6', "vc_get_shared( 'sizes arr' )" );
		return vc_get_shared( 'sizes arr' );
	}
endif;
if ( ! function_exists( 'vc_colors_arr' ) ) :
	/**
	 * Get configuration for colors attribute.
	 *
	 * @return array
	 * @deprecated 8.6
	 */
	function vc_colors_arr() {
		_deprecated_function( __FUNCTION__, '8.6', "vc_get_shared( 'colors arr' )" );
		return vc_get_shared( 'colors arr' );
	}
endif;
if ( ! function_exists( 'vc_target_param_list' ) ) :
	/**
	 * Get configuration for target controls.
	 *
	 * @return array
	 * @deprecated 8.6
	 */
	function vc_target_param_list() {
		_deprecated_function( __FUNCTION__, '8.6', "vc_get_shared( 'target param list' )" );
		return vc_get_shared( 'target param list' );
	}
endif;
if ( ! function_exists( 'vc_siteAttachedImages' ) ) :
	/**
	 *  Helper function which returns list of site attached images, and if image is attached to the current post it adds class 'added'
	 *
	 * @param array $att_ids
	 *
	 * @return string
	 * @since 4.11
	 * @deprecated 8.8
	 */
    function vc_siteAttachedImages( $att_ids = array() ) { // phpcs:ignore
		_deprecated_function( __FUNCTION__, '8.8' );

		$output = '';

		$limit = (int) apply_filters( 'vc_site_attached_images_query_limit', - 1 );
		$media_images = get_posts( 'post_type=attachment&orderby=ID&numberposts=' . $limit );
		foreach ( $media_images as $image_post ) {
			$thumb_src = wp_get_attachment_image_src( $image_post->ID );
			$thumb_src = $thumb_src[0];

			$class = ( in_array( $image_post->ID, $att_ids, true ) ) ? ' class="added"' : '';

			$output .= '<li' . $class . '>
						<img rel="' . esc_attr( $image_post->ID ) . '" src="' . esc_url( $thumb_src ) . '" />
						<span class="img-added">' . esc_html__( 'Added', 'js_composer' ) . '</span>
					</li>';
		}

		if ( '' !== $output ) {
			$output = '<ul class="gallery_widget_img_select">' . $output . '</ul>';
		}

		return $output;
	}
endif;
if ( ! function_exists( 'vc_parse_options_string' ) ) :
	/**
	 * String parser for options.
	 *
	 * @param string $initial_string
	 * @param string $tag
	 * @param string $param
	 *
	 * @return array
	 * @throws \Exception
	 * @since 4.2
	 * @deprecated 8.8
	 */
	function vc_parse_options_string( $initial_string, $tag, $param ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh, CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		_deprecated_function( __FUNCTION__, '8.8' );

		$options = [];
		$option_settings_list = [];
		$settings = WPBMap::getParam( $tag, $param );

		foreach ( preg_split( '/\|/', $initial_string ) as $value ) {
			if ( preg_match( '/\:/', $value ) ) {
				$split = preg_split( '/\:/', $value );
				$option_name = $split[0];
				$option_settings = vc_param_options_get_settings( $option_name, $settings['options'] );
				$option_settings_list[ $option_name ] = $option_settings;
				if ( isset( $option_settings['type'] ) && 'checkbox' === $option_settings['type'] ) {
					$option_value = array_map( 'vc_param_options_parse_values', preg_split( '/\,/', $split[1] ) );
				} else {
					$option_value = rawurldecode( $split[1] );
				}
				$options[ $option_name ] = $option_value;
			}
		}
		if ( isset( $settings['options'] ) ) {
			foreach ( $settings['options'] as $setting_option ) {
				if ( 'separator' !== $setting_option['type'] && isset( $setting_option['value'] ) && empty( $options[ $setting_option['name'] ] ) ) {
					$options[ $setting_option['name'] ] = 'checkbox' === $setting_option['type'] ? preg_split( '/\,/', $setting_option['value'] ) : $setting_option['value'];
				}
				if ( isset( $setting_option['name'] ) && isset( $options[ $setting_option['name'] ] ) && isset( $setting_option['value_type'] ) ) {
					if ( 'integer' === $setting_option['value_type'] ) {
						$options[ $setting_option['name'] ] = (int) $options[ $setting_option['name'] ];
					} elseif ( 'float' === $setting_option['value_type'] ) {
						$options[ $setting_option['name'] ] = (float) $options[ $setting_option['name'] ];
					} elseif ( 'boolean' === $setting_option['value_type'] ) {
						$options[ $setting_option['name'] ] = (bool) $options[ $setting_option['name'] ];
					}
				}
			}
		}

		return $options;
	}
endif;
if ( ! function_exists( 'vc_is_responsive_disabled' ) ) :
	/**
	 * Check if plugin no_resonsive_css settings is disabled.
	 *
	 * @return bool
	 * @deprecated 8.8
	 */
	function vc_is_responsive_disabled() {
		_deprecated_function( __FUNCTION__, '8.8' );

		$disable_responsive = vc_settings()->get( 'not_responsive_css' );

		return '1' === $disable_responsive;
	}
endif;
if ( ! function_exists( 'vc_map_integrate_get_atts' ) ) :
	/**
	 * Retrieves and processes default attributes for integrated shortcodes.
	 *
	 * This function fetches the parameters for a base shortcode and an integrated shortcode,
	 * then processes these parameters to generate a default set of attributes.
	 * The resulting associative array of attributes is returned.
	 *
	 * @param string $base_shortcode
	 * @param string $integrated_shortcode
	 * @param string $field_prefix
	 * @return array
	 * @throws Exception
	 * @since 4.5
	 * @deprecated 8.8
	 */
	function vc_map_integrate_get_atts( $base_shortcode, $integrated_shortcode, $field_prefix = '' ) {
		_deprecated_function( __FUNCTION__, '8.8' );

		$params = vc_map_integrate_get_params( $base_shortcode, $integrated_shortcode, $field_prefix );
		$atts = [];
		if ( is_array( $params ) && ! empty( $params ) ) {
			foreach ( $params as $param ) {
				$value = '';
				if ( isset( $param['value'] ) ) {
					if ( isset( $param['std'] ) ) {
						$value = $param['std'];
					} elseif ( is_array( $param['value'] ) ) {
						reset( $param['value'] );
						$value = current( $param['value'] );
					} else {
						$value = $param['value'];
					}
				}
				$atts[ $param['param_name'] ] = $value;
			}
		}

		return $atts;
	}
endif;
if ( ! function_exists( 'vc_map_add_css_animation' ) ) :
	/**
	 * Get CSS animation for shortcode params.
	 *
	 * @param bool $label
	 * @return array
	 * @deprecated 9.0 use vc_config()->get_css_animation()
	 */
	function vc_map_add_css_animation( $label = true ) {
		_deprecated_function( __FUNCTION__, '9.0', 'vc_config()->get_css_animation()' );

		return vc_config()->get_css_animation( $label );
	}
endif;
if ( ! function_exists( 'vc_navbar_undoredo' ) ) :
	/**
	 * Undo Redo navbar for frontend editor.
	 *
	 * @deprecated 9.0 without replacement
	 */
	function vc_navbar_undoredo() {
		_deprecated_function( __FUNCTION__, '9.0' );

		/**
		 * Class Vc_Navbar_Undoredo
		 *
		 * @deprecated 9.0
		 */
		class Vc_Navbar_Undoredo {
			/**
			 * Vc_Navbar_Undoredo constructor.
			 */
			public function __construct() {
				// Backend.
				add_filter( 'vc_nav_controls', [
					$this,
					'addControls',
				] );

				// Frontend.
				add_filter( 'vc_nav_front_controls', [
					$this,
					'addControls',
				] );
			}

			/**
			 * Add undo/redo controls.
			 *
			 * @param array $controls
			 * @return array
			 */
			public function addControls( $controls ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
				$undo_title = esc_attr( wpb_get_title_with_shortcut( 'Undo' ) );
				$redo_title = esc_attr( wpb_get_title_with_shortcut( 'Redo' ) );
				$controls[] = [
					'undo',
					'<li class="vc_hide-mobile vc_hide-desktop-more">
                        <a id="vc_navbar-undo" class="vc_icon-btn vc_undo-redo vc_undo-button vc_hide-mobile" disabled tabindex="0" title="' . $undo_title . '" role="button" aria-label="' . $undo_title . '">
                            <i class="vc-composer-icon vc-c-icon-undo" aria-hidden="true"></i>
                            <p class="vc_hide-desktop" aria-hidden="true">' . __( 'Undo', 'js_composer' ) . '</p>
                        </a>
                    </li>',
				];
				$controls[] = [
					'redo',
					'<li class="vc_hide-mobile vc_hide-desktop-more">
                        <a id="vc_navbar-redo" class="vc_icon-btn vc_undo-redo vc_redo-button vc_hide-mobile" disabled tabindex="0" title="' . $redo_title . '" role="button" aria-label="' . $redo_title . '">
                            <i class="vc-composer-icon vc-c-icon-redo" aria-hidden="true"></i>
                            <p class="vc_hide-desktop" aria-hidden="true">' . __( 'Redo', 'js_composer' ) . '</p>
                        </a>
                    </li>',
				];

				return $controls;
			}
		}
	}
endif;
