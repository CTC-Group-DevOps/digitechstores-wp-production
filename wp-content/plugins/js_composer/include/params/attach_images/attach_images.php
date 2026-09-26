<?php
/**
 * Param type 'attach_images'.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WpbParamAttachImages
 *
 * @since 9.0
 */
class WpbParamAttachImages {
	/**
	 * Render param's HTML.
	 *
	 * @since 9.0
	 * @param array $settings Param settings.
	 * @param string $value Param value.
	 * @param string $tag WPBakery shortcode tag.
	 * @param string $param_id Param ID.
	 *
	 * @return string
	 */
	public function render( $settings, $value, $tag = '', $param_id = '' ) {
		$param_data = $this->get_param_data( $settings, $value );

		return WPB_Form_Field_Attach_Images::get( // nosemgrep - escaping handled by WPB_Form_Field_Attach_Images.
			[
				'value' => $value,
				'image_id_list' => $param_data['image_id_list'],
				'classes' => wpbakery()->editForm()->get_value_control_classes(
					$settings['param_name'],
					$settings['type'],
				),
				'images' => $param_data['images'],
				'name' => $settings['param_name'],
				'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
				'is_link_icon' => $this->is_link_icon( $settings ),
				'images_output' => $this->get_images_output( $param_data['images'], $this->is_link_icon( $settings ), $value ),
			]
		);
	}

	/**
	 * Get images output HTML.
	 *
	 * @since 9.0
	 * @param array $images
	 * @param bool $is_link_icon
	 * @param string $value
	 * @return string
	 */
	public function get_images_output( $images, $is_link_icon, $value ) {
		$output = '';
		$value = is_null( $value ) ? '' : $value;
		$image_data = json_decode( $value, true );
		foreach ( $images as $image_id ) {
			$thumb_src = wpb_get_image_thumb( $image_id );

			if ( $thumb_src ) {
				$output .= vc_get_template('form-fields/attach_image/attach_images_single_image.php', [
					'label_remove' => esc_attr__( 'Remove', 'js_composer' ),
					'is_template' => false,
					'image' => $image_id,
					'thumb_src' => $thumb_src,
					'is_link_icon' => $is_link_icon,
					'link' => is_array( $image_data ) && isset( $image_data[ $image_id ] ) ? $image_data[ $image_id ] : [],
				] );
			}
		}

		return $output;
	}

	/**
	 * Get param render data.
	 *
	 * @since 9.0
	 * @param array $settings
	 * @param string $value
	 * @return array
	 */
	public function get_param_data( $settings, $value ) {
		if ( $this->is_link_icon( $settings ) ) {
			$link_data = json_decode( $value, true );
			if ( is_array( $link_data ) ) {
				$ids_string = implode( ',', array_keys( $link_data ) );
			} else {
				$ids_string = (string) $value;
			}
			$image_id_list = wpb_removeNotExistingImgIDs( $ids_string );
		} else {
			$image_id_list = wpb_removeNotExistingImgIDs( $value );
		}

		$images = '' !== $image_id_list ? explode( ',', $image_id_list ) : [];

		return [
			'image_id_list' => $image_id_list,
			'images' => $images,
		];
	}

	/**
	 * Check if param has is_link_icon setting enabled.
	 *
	 * @since 9.0
	 * @param array $settings
	 * @return bool
	 */
	public function is_link_icon( $settings ) {
		return $settings['settings']['is_link_icon'] ?? false;
	}
}
