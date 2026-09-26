<?php
/**
 * Attach image form field class.
 *
 * Handles attach_image specific logic and rendering.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPB_Form_Field_Attach_Image
 *
 * Attach image form field implementation.
 *
 * @since 9.0
 */
class WPB_Form_Field_Attach_Image extends WPB_Form_Field_Abstract {
	/**
	 * Get the field type identifier.
	 *
	 * @since 9.0
	 *
	 * @return string Field type.
	 */
	protected function get_type() {
		return 'attach_image';
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
			'classes' => '',
			'id' => '',
			'param_value' => '',
			'thumb_src' => '',
			'name' => '',
			'label_upload' => __( 'Upload', 'js_composer' ),
			'label_edit' => __( 'Edit', 'js_composer' ),
			'label_remove' => __( 'Remove', 'js_composer' ),
			'is_link_icon' => false,
			'link' => [],
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
		return 'form-fields/' . $type . '/' . $type . '.php';
	}
}
