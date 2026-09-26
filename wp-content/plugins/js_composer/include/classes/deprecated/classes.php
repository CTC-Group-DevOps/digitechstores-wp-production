<?php
/**
 * Class lib
 *
 * @deprecated
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Autocomplete class
 *
 * @since 4.4
 * @deprecated 9.0 Use WPB_Form_Field_Autocomplete instead
 */
class Vc_AutoComplete {
	/**
	 * Param settings
	 *
	 * @since 4.4
	 * @var array $settings
	 */
	protected $settings;
	/**
	 * Current param value (if multiple it is splitted by ',' comma to make array).
	 *
	 * @since 4.4
	 * @var string $value
	 */
	protected $value;
	/**
	 * Shortcode name(base).
	 *
	 * @since 4.4
	 * @var string $tag
	 */
	protected $tag;

	/**
	 * Vc_AutoComplete constructor.
	 *
	 * @param array $settings - param settings (from vc_map).
	 * @param string $value - current param value.
	 * @param string $tag - shortcode name(base).
	 *
	 * @since 4.4
	 * @deprecated 9.0 Use WPB_Form_Field_Autocomplete instead
	 */
	public function __construct( $settings, $value, $tag ) {
		_deprecated_function( __METHOD__, '9.0', 'WPB_Form_Field_Autocomplete::get()' );
		_doing_it_wrong( __CLASS__, 'This class is deprecated and will be removed in version 10.0. Use WPB_Form_Field_Autocomplete instead.', '9.0' );

		$this->tag = $tag;
		$this->settings = $settings;
		$this->value = $value;
	}

	/**
	 * Render autocomplete param.
	 *
	 * @param string $param_id since 9.0.
	 * @return string
	 * @since 4.4
	 * @deprecated 9.0 Use WPB_Form_Field_Autocomplete::get() instead
	 * vc_filter: vc_autocomplete_{shortcode_tag}_{param_name}_render - hook to define output for autocomplete item
	 */
	public function render( $param_id = '' ) {
		$settings_array = isset( $this->settings['settings'] ) ? $this->settings['settings'] : [];

		return WPB_Form_Field_Autocomplete::get( [ // nosemgrep - escaping handled by WPB_Form_Field_Autocomplete.
			'id'             => wpbakery()->editForm()->get_value_control_id( $param_id, $this->settings['type'] ),
			'classes'        => wpbakery()->editForm()->get_value_control_classes( $this->settings['param_name'], $this->settings['type'] ),
			'name'           => $this->settings['param_name'],
			'value'          => $this->value,
			'tag'            => $this->tag,
			'settings'       => $settings_array,
			'param_settings' => $this->settings,
		] );
	}
}
