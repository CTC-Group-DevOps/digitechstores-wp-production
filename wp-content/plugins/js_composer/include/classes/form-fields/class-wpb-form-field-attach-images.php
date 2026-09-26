<?php
/**
 * Attach images form field class.
 *
 * Handles attach_images specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Attach_Images
 *
 * Attach images form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Attach_Images extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'attach_images';
	}

	/**
	 * Get default values for arguments.
	 *
	 * @since 9.0
	 *
	 * @return array Default argument values.
	 */
	protected function get_defaults() {
		return [
			'value' => '',
			'image_id_list' => '',
			'classes' => '',
			'id' => '',
			'name' => '',
			'images' => [],
			'label_reorder' => __( 'Reorder', 'js_composer' ),
			'label_remove' => __( 'Remove', 'js_composer' ),
			'is_link_icon' => false,
			'images_output' => false,
		];
	}

	/**
	 * Get the base template path for form fields.
	 *
	 * @since 9.0
	 *
	 * @param string $type
	 * @return string Template base path.
	 */
	protected function get_template_path( $type ) {
		return 'form-fields/attach_image/' . $type . '.php';
	}
}
