<?php
/**
 * Class that handles specific [vc_btn] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_btn.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Btn
 *
 * @since 4.5
 */
class WPBakeryShortCode_Vc_Btn extends WPBakeryShortCode {
	/**
	 * Get CSS file names for vc_btn shortcode.
	 *
	 * @since 9.0
	 * @return array
	 */
	public function get_shortcode_css_files() {
		return [ 'vc_button3', 'vc_button3_editform' ];
	}

	/**
	 * Convert old attributes to new attributes.
	 *
	 * @param array $atts
	 * @return mixed
	 */
	public static function convertAttributesToButton3( $atts ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh, CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		// size btn1 to size btn2.
		$btn1_sizes = [
			'wpb_regularsize',
			'btn-large',
			'btn-small',
			'btn-mini',
		];
		if ( isset( $atts['size'] ) && in_array( $atts['size'], $btn1_sizes, true ) ) {
			$atts['size'] = str_replace( $btn1_sizes, [
				'md',
				'lg',
				'sm',
				'xs',
			], $atts['size'] );
		}

		// Convert Btn1 href+target attributes to Btn2 `link` attribute.
		if ( ! isset( $atts['link'] ) && isset( $atts['href'] ) && strlen( $atts['href'] ) > 0 ) {
			$link = $atts['href'];
			$target = isset( $atts['target'] ) ? $atts['target'] : '';
			$title = isset( $atts['title'] ) ? $atts['title'] : $link;
			$atts['link'] = 'url:' . rawurlencode( $link ) . '|title:' . $title . ( strlen( $target ) > 0 ? '|target:' . rawurlencode( $target ) : '' );
		}

		if ( ( ! isset( $atts['add_icon'] ) || 'true' !== $atts['add_icon'] ) && isset( $atts['icon'] ) && strlen( $atts['icon'] ) > 0 && 'none' !== $atts['icon'] ) {
			// old icon from btn1 is set, let's convert it to new btn.
			$atts['add_icon'] = 'true';
			$atts['icon_type'] = 'pixelicons';
			$atts['icon_align'] = 'right';
			$atts['icon_pixelicons'] = 'vc_pixel_icon vc_pixel_icon-' . str_replace( 'wpb_', '', $atts['icon'] );
		}
		$haystack = [
			'rounded',
			'square',
			'round',
			'outlined',
			'square_outlined',
		];
		if ( isset( $atts['style'] ) && in_array( $atts['style'], $haystack, true ) ) {
			switch ( $atts['style'] ) {
				case 'rounded':
					$atts['style'] = 'flat';
					$atts['shape'] = 'rounded';
					break;
				case 'square':
					$atts['style'] = 'flat';
					$atts['shape'] = 'square';
					break;
				case 'round':
					$atts['style'] = 'flat';
					$atts['shape'] = 'round';
					break;
				case 'outlined':
					$atts['style'] = 'outline';
					break;
				case 'square_outlined':
					$atts['style'] = 'outline';
					$atts['shape'] = 'square';
					break;
			}
		}

		return $atts;
	}

	/**
	 * Title html output.
	 *
	 * @param string $title
	 *
	 * @return string
	 * @since 4.5
	 */
	protected function outputTitle( $title ) {
		$icon = $this->settings( 'icon' );

		return '<h4 class="wpb_element_title"><span class="vc_general vc_element-icon vc_btn3-icon' . ( ! empty( $icon ) ? ' ' . $icon : '' ) . '"></span></h4>';
	}

	/**
	 * Resolve link attributes for a custom URL link type.
	 *
	 * @param array $atts
	 * @return array
	 */
	public function resolve_link_custom( $atts ) {
		$atts['link'] = isset( $atts['url'] ) ? $atts['url'] : '';
		return $atts;
	}

	/**
	 * Resolve link attributes for a post permalink link type.
	 *
	 * @param array        $atts
	 * @param WP_Post|null $post
	 * @param string       $target_suffix
	 * @return array
	 */
	public function resolve_link_post_link( $atts, $post, $target_suffix ) {
		if ( $post instanceof WP_Post ) {
			$atts['link'] = 'url:' . rawurlencode( get_permalink( $post->ID ) ) . $target_suffix;
		}
		return $atts;
	}

	/**
	 * Resolve link attributes for a post thumbnail image link type.
	 *
	 * @param array        $atts
	 * @param WP_Post|null $post
	 * @param string       $target_suffix
	 * @return array
	 */
	public function resolve_link_image( $atts, $post, $target_suffix ) {
		$href = vc_gitem_template_attribute_post_image_url( '', [
			'post' => $post,
			'data' => '',
		] );
		if ( ! empty( $href ) ) {
			$atts['link'] = 'url:' . rawurlencode( $href ) . $target_suffix;
		}
		return $atts;
	}

	/**
	 * Resolve link attributes for a full-size post image link type.
	 *
	 * @param array        $atts
	 * @param WP_Post|null $post
	 * @param string       $target_suffix
	 * @return array
	 */
	public function resolve_link_image_full( $atts, $post, $target_suffix ) {
		$href = vc_gitem_template_attribute_post_full_image_url( '', [
			'post' => $post,
			'data' => '',
		] );
		if ( ! empty( $href ) ) {
			$atts['link'] = 'url:' . rawurlencode( $href ) . $target_suffix;
		}
		return $atts;
	}

	/**
	 * Resolve link attributes for a full-size image lightbox link type.
	 *
	 * @param array        $atts
	 * @param WP_Post|null $post
	 * @return array
	 */
	public function resolve_link_image_lightbox( $atts, $post ) {
		$href = vc_gitem_template_attribute_post_full_image_url( '', [
			'post' => $post,
			'data' => '',
		] );
		if ( ! empty( $href ) ) {
			$atts['link']              = 'url:' . rawurlencode( $href );
			$atts['vc_gitem_lightbox'] = 'lightbox[rel-' . md5( vc_request_param( 'shortcode_id' ) ) . ']';
		}
		return $atts;
	}

	/**
	 * Resolve link attributes for a thumbnail image lightbox link type.
	 *
	 * @param array        $atts
	 * @param WP_Post|null $post
	 * @return array
	 */
	public function resolve_link_image_full_lightbox( $atts, $post ) {
		$href = vc_gitem_template_attribute_post_image_url( '', [
			'post' => $post,
			'data' => '',
		] );
		if ( ! empty( $href ) ) {
			$atts['link']              = 'url:' . rawurlencode( $href );
			$atts['vc_gitem_lightbox'] = 'lightbox[rel-' . md5( vc_request_param( 'shortcode_id' ) ) . ']';
		}
		return $atts;
	}
}
