<?php
/**
 * Class that handles specific [vc_raw_html] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_raw_html.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Raw_Html
 */
class WPBakeryShortCode_Vc_Raw_Html extends WPBakeryShortCode {

	/**
	 * Add params html holders.
	 *
	 * @param array $param
	 * @param string $value
	 * @return string
	 */
	public function singleParamHtmlHolder( $param, $value ) {
		$output = '';
		// Compatibility fixes
		// TODO: check $old_names & &new_names. Leftover from copypasting?
		$old_names = [
			'yellow_message',
			'blue_message',
			'green_message',
			'button_green',
			'button_grey',
			'button_yellow',
			'button_blue',
			'button_red',
			'button_orange',
		];
		$new_names = [
			'alert-block',
			'alert-info',
			'alert-success',
			'btn-success',
			'btn',
			'btn-info',
			'btn-primary',
			'btn-danger',
			'btn-warning',
		];
		$value = str_ireplace( $old_names, $new_names, $value );

		$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
		$type = isset( $param['type'] ) ? $param['type'] : '';
		$class = isset( $param['class'] ) ? $param['class'] : '';

		if ( isset( $param['holder'] ) && 'hidden' !== $param['holder'] ) {
			if ( 'textarea_ace' === $param['type'] ) {
				$output .= sprintf(
					'<%s class="wpb_vc_param_value %s %s %s" name="%s">%s</%s>%s',
					$param['holder'],
					$param_name,
					$type,
					$class,
					$param_name,
					htmlentities( rawurldecode( base64_decode( wp_strip_all_tags( $value ) ) ), ENT_COMPAT, 'UTF-8' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
					$param['holder'],
					WPB_Form_Field_Hidden::get([
						'name' => $param_name . '_code',
						'classes' => $param_name . '_code',
						'value' => wp_strip_all_tags( $value ),
						'is_value_escape' => false,
					]),
				);

			} else {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			}
		}
		return $output;
	}
}
