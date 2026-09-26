<?php
/**
 * Class that handles specific [vc_gitem_post_data] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_gitem_post_data.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-custom-heading.php' );

/**
 * Class WPBakeryShortCode_Vc_Gitem_Post_Data
 */
class WPBakeryShortCode_Vc_Gitem_Post_Data extends WPBakeryShortCode_Vc_Custom_heading {
	/**
	 * Get data_source attribute value
	 *
	 * @param array $atts - list of shortcode attributes.
	 *
	 * @return string
	 */
	public function getDataSource( array $atts ) {
		return isset( $atts['data_source'] ) ? $atts['data_source'] : 'post_title';
	}

	/**
	 * Get attributes for shortcode.
	 *
	 * @param array $atts
	 * @return array
	 * @throws \Exception
	 */
	public function getAttributes( $atts ) {
		// b.c for 9.0.
		$atts = $this->extract_field_from_container( $atts, 'text_align', 'font_container' );
		$atts = $this->extract_field_from_container( $atts, 'color', 'block_container' );

		$color = $atts['color'] ?? '';
		$text_align = $atts['text_align'] ?? '';
		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		if ( isset( $atts['block_container'] ) && strlen( $atts['block_container'] ) > 0 ) {
			if ( ! isset( $atts['font_container'] ) ) {
				$atts['font_container'] = $atts['block_container'];
			} else {
				// merging two params into font_container.
				$atts['font_container'] .= '|' . $atts['block_container'];
			}
		}
		$atts = parent::getAttributes( $atts );
		if ( ! isset( $this->atts['use_custom_fonts'] ) || 'yes' !== $this->atts['use_custom_fonts'] ) {
			$atts['google_fonts_data'] = [];
		}
		$atts['color'] = $color;
		$atts['text_align'] = $text_align;

		return $atts;
	}

	/**
	 * Extract a field from a pipe-delimited container attribute into a standalone attribute.
	 *
	 * @param array  $atts
	 * @param string $field_key
	 * @param string $container_key
	 * @return array
	 */
	protected function extract_field_from_container( $atts, $field_key, $container_key ) {
		if ( ! empty( $atts[ $field_key ] ) || empty( $atts[ $container_key ] ) ) {
			return $atts;
		}

		$parsed = vc_parse_multi_attribute( $atts[ $container_key ] );
		if ( empty( $parsed[ $field_key ] ) ) {
			return $atts;
		}

		$atts[ $field_key ] = $parsed[ $field_key ];
		unset( $parsed[ $field_key ] );
		$parts = [];
		foreach ( $parsed as $key => $value ) {
			$parts[] = $key . ':' . $value;
		}
		$atts[ $container_key ] = implode( '|', $parts );

		return $atts;
	}
}
