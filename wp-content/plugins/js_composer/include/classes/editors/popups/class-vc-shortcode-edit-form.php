<?php
/**
 * WPBakery Page Builder main class.
 *
 * @package WPBakeryPageBuilder
 * @since   4.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Edit form for shortcodes with ability to manage shortcode attributes in more convenient way.
 *
 * @since 4.2
 */
class Vc_Shortcode_Edit_Form {
	/**
	 * Indicates whether the class has been initialized.
	 *
	 * @var bool
	 * @since 4.2
	 */
	protected $initialized;

	/**
	 * Initialize the class, including setting up actions and filters.
	 *
	 * @since 4.2
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}
		$this->initialized = true;

		add_action( 'wp_ajax_vc_edit_form', [
			$this,
			'renderFields',
		] );

		add_filter( 'vc_single_param_edit', [
			$this,
			'changeEditFormFieldParams',
		] );
		add_filter( 'vc_edit_form_class', [
			$this,
			'changeEditFormParams',
		] );
		add_action( 'wp_ajax_wpb_update_element_usage_count', [
			$this,
			'updateElementUsageCount',
		] );
	}

	/**
	 * Render the edit form template.
	 */
	public function render() {
		vc_include_template( 'editors/popups/vc_ui-panel-edit-element.tpl.php', [
			'id' => 'edit-element',
			'box' => $this,
			'controls' => $this->getPopupControls(),
			'inner_classes' => get_option( 'wpb_js_auto_save' ) ? 'vc_ui-panel-window-inner--auto-save' : '',
		] );
	}

	/**
	 * Get popup controls.
	 *
	 * @since 8.1
	 * @return array
	 */
	public function getPopupControls() {
		$controls = [
			'panel-minimize' => [ 'title' => esc_html__( 'Minimize', 'js_composer' ) ],
			'close'   => [ 'title' => esc_html__( 'Close', 'js_composer' ) ],
		];

		if ( vc_user_access()->part( 'presets' )->checkStateAny( true, null )->get() ||
			vc_user_access()->part( 'templates' )->checkStateAny( true, null )->get() ) {
			$controls = array_merge(
				[
					'settings' => [ 'template' => 'editors/partials/vc_ui-settings-dropdown.tpl.php' ],
				],
				$controls );
		}

		return $controls;
	}

	/**
	 * Build edit form fields.
	 *
	 * @since 4.4
	 */
	public function renderFields() {
		$tag = vc_post_param( 'tag' );

		if ( ! WPBMap::exists( $tag ) ) {
			wp_send_json_error( esc_html__( 'Shortcode is not registered in WPBakery Page Builder', 'js_composer' ) );
		}

		vc_user_access()->checkAdminNonce()->validateDie( esc_html__( 'Access denied', 'js_composer' ) )->wpAny( [
			'edit_post',
			(int) vc_request_param( 'post_id' ),
		] )->validateDie( esc_html__( 'Access denied', 'js_composer' ) )->check( 'vc_user_access_check_shortcode_edit', $tag )->validateDie( esc_html__( 'Access denied', 'js_composer' ) );

		$params = (array) stripslashes_deep( vc_post_param( 'params' ) );
		$params = array_map( 'vc_htmlspecialchars_decode_deep', $params );

		$shortcode_atts = (array) stripslashes_deep( vc_post_param( 'shortcode_atts' ) );
		$shortcode_atts = array_map( 'vc_htmlspecialchars_decode_deep', $shortcode_atts );

		require_once vc_path_dir( 'EDITORS_DIR', 'class-vc-edit-form-fields.php' );
		$fields = new Vc_Edit_Form_Fields( $tag, $params, $shortcode_atts );
		$fields->render();
		wp_die();
	}

	/**
	 * We need to update usage count for element on every new adding of element.
	 * This is required for most used elements sorting.
	 *
	 * @return void
	 */
	public function updateElementUsageCount() {
		vc_user_access()->checkAdminNonce()->validateDie();

		$tag = sanitize_key( vc_post_param( 'tag' ) );
		if ( $tag ) {
			$usage_count = get_option( 'wpb_usage_count', [] );
			$usage_count[ $tag ] = isset( $usage_count[ $tag ] ) ? $usage_count[ $tag ] + 1 : 1;

			update_option( 'wpb_usage_count', $usage_count );
		}
		wp_die();
	}

	/**
	 * Modify the parameters for editing form fields.
	 *
	 * @param array $param
	 *
	 * @return mixed
	 */
	public function changeEditFormFieldParams( $param ) {
		$css = $param['vc_single_param_edit_holder_class'];
		if ( isset( $param['edit_field_class'] ) ) {
			$new_css = $param['edit_field_class'];
		} else {
			$new_css = 'vc_col-xs-12';
		}
		array_unshift( $css, $new_css );
		$param['vc_single_param_edit_holder_class'] = $css;

		return $param;
	}

	/**
	 * Modify the CSS classes for the edit form.
	 *
	 * @param array $css_classes
	 *
	 * @return mixed
	 */
	public function changeEditFormParams( $css_classes ) {
		$css = '';
		array_unshift( $css_classes, $css );

		return $css_classes;
	}

	/**
	 * Get element param value control attribute class.
	 *
	 * @param string $param_name
	 * @param string $type
	 * @param string $additional_classes
	 * @param string $class_after_wpb_vc_param_value
	 * @since 9.0
	 * @return string
	 */
	public function get_value_control_attr_class( $param_name, $type, $additional_classes = '', $class_after_wpb_vc_param_value = '' ) {
		return ' class="' . $this->get_value_control_classes( $param_name, $type, $additional_classes, $class_after_wpb_vc_param_value ) . '" ';
	}

	/**
	 * Output element param value control attribute class.
	 *
	 * @param string $param_name
	 * @param string $type
	 * @param string $additional_classes
	 * @param string $class_after_wpb_vc_param_value
	 * @since 9.0
	 */
	public function output_value_control_attr_class( $param_name, $type, $additional_classes = '', $class_after_wpb_vc_param_value = '' ) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->get_value_control_attr_class( $param_name, $type, $additional_classes, $class_after_wpb_vc_param_value );
	}

	/**
	 * Get element param value control attribute id.
	 *
	 * @param string $type
	 * @param string $param_id
	 * @param string $additional_prefix
	 * @since 9.0
	 * @return string
	 */
	public function get_value_control_attr_id( $type, $param_id, $additional_prefix = '' ) {
		return ' id="' . esc_attr( $this->get_value_control_id( $type, $param_id, $additional_prefix ) ) . '" ';
	}

	/**
	 * Output element param value control attribute id.
	 *
	 * @param string $type
	 * @param string $param_id
	 * @param string $additional_prefix
	 * @since 9.0
	 */
	public function output_value_control_attr_id( $type, $param_id, $additional_prefix = '' ) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->get_value_control_attr_id( $type, $param_id, $additional_prefix );
	}

	/**
	 * Get element param value control classes.
	 *
	 * @since 9.0
	 * @param string $param_name
	 * @param string $type
	 * @param string $additional_classes
	 * @param string $class_after_wpb_vc_param_value
	 * @return string
	 */
	public function get_value_control_classes( $param_name, $type, $additional_classes = '', $class_after_wpb_vc_param_value = '' ) {
		return sprintf( 'wpb_vc_param_value%s %s %s%s',
			'' !== $class_after_wpb_vc_param_value ? ' ' . esc_attr( $class_after_wpb_vc_param_value ) : '',
			esc_attr( $param_name ),
			esc_attr( $type ),
			'' !== $additional_classes ? ' ' . esc_attr( $additional_classes ) : ''
		);
	}

	/**
	 * Get element param value control ID.
	 *
	 * @param string $param_id
	 * @param string $type
	 * @param string $additional_prefix
	 * @return string
	 */
	public function get_value_control_id( $param_id, $type, $additional_prefix = '' ) {
		return esc_attr( $type ) . '_' .
				esc_attr( $param_id ) .
				( '' !== $additional_prefix ? '_' . esc_attr( $additional_prefix ) : '' );
	}
}
