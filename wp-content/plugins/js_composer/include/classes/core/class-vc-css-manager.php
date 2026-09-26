<?php
/**
 * CSS Manager class for handling CSS loading strategies.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Vc_Css_Manager class.
 *
 * Manages CSS loading strategies and determines which CSS files to load
 * based on the configured strategy.
 *
 * @since 9.0
 */
class Vc_Css_Manager {
	/**
	 * Get the CSS loading strategy from settings.
	 *
	 * @since 9.0
	 * @return string The CSS loading strategy.
	 */
	public static function get_css_loading_strategy() {
		$css_loading = vc_settings()->get( 'css_loading' );
		if ( empty( $css_loading ) ) {
			return 'legacy';
		}
		return $css_loading;
	}

	/**
	 * Check if new CSS bundles should be loaded for a specific post.
	 *
	 * @since 9.0
	 * @param int|null $post_id The post ID to check. If null, uses current post.
	 * @return bool True if new CSS should be loaded, false otherwise.
	 */
	public static function should_load_optimized_css( $post_id = null ) {
		$strategy = self::get_css_loading_strategy();

		switch ( $strategy ) {
			case 'optimized':
				return true;

			case 'hybrid':
				return self::should_load_optimized_css_for_hybrid( $post_id );

			default:
				return false;
		}
	}

	/**
	 * Check if optimized CSS should be loaded in hybrid mode.
	 *
	 * @since 9.0
	 * @param int|null $post_id The post ID to check. If null, uses current post.
	 * @return bool True if optimized CSS should be loaded, false otherwise.
	 */
	protected static function should_load_optimized_css_for_hybrid( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return false;
		}

		$post = get_post( $post_id );
		if ( $post && 'auto-draft' === $post->post_status ) {
			update_post_meta( $post_id, '_wpb_vc_use_optimized_css', '1' );
			return true;
		}

		$use_optimized_css = get_post_meta( $post_id, '_wpb_vc_use_optimized_css', true );
		if ( '' === $use_optimized_css ) {
			return false;
		}

		return '1' === $use_optimized_css;
	}

	/**
	 * Get the appropriate frontend CSS file based on the CSS loading strategy.
	 *
	 * @since 9.0
	 * @param int|null $post_id The post ID to check. If null, uses current post.
	 * @return string The CSS file path.
	 */
	public static function get_frontend_css_file( $post_id = null ) {
		if ( self::should_load_optimized_css( $post_id ) ) {
			return vc_asset_url( 'css/js_composer_optimized.min.css' );
		}
		return vc_asset_url( 'css/js_composer.min.css' );
	}

	/**
	 * Get the appropriate backend editor CSS file based on the CSS loading strategy.
	 *
	 * @since 9.0
	 * @param int|null $post_id The post ID to check. If null, uses current post.
	 * @return string The CSS file path.
	 */
	public static function get_backend_editor_css_file( $post_id = null ) {
		if ( self::should_load_optimized_css( $post_id ) ) {
			return vc_asset_url( 'css/js_composer_backend_editor_optimized.min.css' );
		}
		return vc_asset_url( 'css/js_composer_backend_editor.min.css' );
	}
}
