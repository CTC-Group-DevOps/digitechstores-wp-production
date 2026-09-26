<?php
/**
 * Param type 'attach_image'.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WpbParamAttachImage
 *
 * @since 9.0
 */
class WpbParamAttachImage {
	/**
	 * Render param's HTML.
	 *
	 * @param array $settings Param settings.
	 * @param string $value Param value.
	 * @param string $tag WPBakery shortcode tag.
	 * @param string $param_id Param ID.
	 *
	 * @return string
	 */
	public function render( $settings, $value, $tag = '', $param_id = '' ) {
		$param_data = $this->get_param_data( $settings, $value );

		return WPB_Form_Field_Attach_Image::get( // nosemgrep - escaping handled by WPB_Form_Field_Attach_Image.
			[
				'param_value' => $param_data['param_value'],
				'image_id' => $param_data['image_id'],
				'classes' => wpbakery()->editForm()->get_value_control_classes(
					$settings['param_name'],
					$settings['type'],
				),
				'thumb_src' => $param_data['thumb_src'],
				'name' => $settings['param_name'],
				'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
				'is_link_icon' => $settings['settings']['is_link_icon'] ?? false,
				'link' => $param_data['link'],
			]
		);
	}

	/**
	 * Get param render data.
	 *
	 * @param array $settings
	 * @param string $value
	 * @return array
	 */
	public function get_param_data( $settings, $value ) {
		$link = [];
		if ( $this->is_link_icon( $settings ) ) {
			$link_data = json_decode( $value, true );
			if ( is_array( $link_data ) ) {
				$image_id = array_key_first( $link_data );
				$link = $link_data[ $image_id ];
			} else {
				$image_id = $link_data;
			}
			$image_id = wpb_removeNotExistingImgIDs( $image_id );
			if ( '' !== $image_id ) {
				$param_value = $value;
			} else {
				$param_value = '';
			}
		} else {
			$image_id = wpb_removeNotExistingImgIDs( $value );
			$param_value = $image_id;
		}

		$thumb_src = '';
		if ( '' !== $image_id ) {
			$thumb_src = wpb_get_image_thumb( $image_id, 'medium' );
		}

		return [
			'param_value' => $param_value,
			'thumb_src' => $thumb_src,
			'link' => $link,
			'image_id' => $image_id,
		];
	}

	/**
	 * Check if param has is_link_icon setting enabled.
	 *
	 * @param array $settings
	 * @return bool
	 */
	public function is_link_icon( $settings ) {
		return $settings['settings']['is_link_icon'] ?? false;
	}
}
