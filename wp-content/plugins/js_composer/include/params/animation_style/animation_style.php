<?php
/**
 * Param type 'animation_style'
 *
 * Used to create dropdown field with animation styles.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/#vc_map()-ParametersofparamsArray
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_ParamAnimation
 *
 * For working with animations
 * array(
 *        'type' => 'animation_style',
 *        'heading' => esc_html__( 'Animation', 'js_composer' ),
 *        'param_name' => 'animation',
 * ),
 * Preview in https://daneden.github.io/animate.css/
 *
 * @since 4.4
 */
class Vc_ParamAnimation {
	/**
	 * Parameter settings from vc_map.
	 *
	 * @since 4.4
	 * @var array $settings
	 */
	protected $settings;
	/**
	 * Parameter value.
	 *
	 * @since 4.4
	 * @var string $value
	 */
	protected $value;

	/**
	 * Define available animation effects.
	 *
	 * @since 4.4
	 * @see vc_filter: vc_param_animation_style_list - to override animation styles array.
	 * @return array
	 */
	protected function animationStyles() {
		$styles = [
			[
				'values' => [
					__( 'None', 'js_composer' ) => 'none',
				],
				'label' => __( 'None', 'js_composer' ),
			],
			[
				'label' => __( 'Attention Seekers', 'js_composer' ),
				'values' => [
					// text to display => value.
					__( 'bounce', 'js_composer' ) => [
						'value' => 'bounce',
						'type' => 'other',
					],
					__( 'flash', 'js_composer' ) => [
						'value' => 'flash',
						'type' => 'other',
					],
					__( 'pulse', 'js_composer' ) => [
						'value' => 'pulse',
						'type' => 'other',
					],
					__( 'rubberBand', 'js_composer' ) => [
						'value' => 'rubberBand',
						'type' => 'other',
					],
					__( 'shake', 'js_composer' ) => [
						'value' => 'shake',
						'type' => 'other',
					],
					__( 'swing', 'js_composer' ) => [
						'value' => 'swing',
						'type' => 'other',
					],
					__( 'tada', 'js_composer' ) => [
						'value' => 'tada',
						'type' => 'other',
					],
					__( 'wobble', 'js_composer' ) => [
						'value' => 'wobble',
						'type' => 'other',
					],
				],
			],
			[
				'label' => __( 'Bouncing Entrances', 'js_composer' ),
				'values' => [
					// text to display => value.
					__( 'bounceIn', 'js_composer' ) => [
						'value' => 'bounceIn',
						'type' => 'in',
					],
					__( 'bounceInDown', 'js_composer' ) => [
						'value' => 'bounceInDown',
						'type' => 'in',
					],
					__( 'bounceInLeft', 'js_composer' ) => [
						'value' => 'bounceInLeft',
						'type' => 'in',
					],
					__( 'bounceInRight', 'js_composer' ) => [
						'value' => 'bounceInRight',
						'type' => 'in',
					],
					__( 'bounceInUp', 'js_composer' ) => [
						'value' => 'bounceInUp',
						'type' => 'in',
					],
				],
			],
			[
				'label' => __( 'Bouncing Exits', 'js_composer' ),
				'values' => [
					// text to display => value.
					__( 'bounceOut', 'js_composer' ) => [
						'value' => 'bounceOut',
						'type' => 'out',
					],
					__( 'bounceOutDown', 'js_composer' ) => [
						'value' => 'bounceOutDown',
						'type' => 'out',
					],
					__( 'bounceOutLeft', 'js_composer' ) => [
						'value' => 'bounceOutLeft',
						'type' => 'out',
					],
					__( 'bounceOutRight', 'js_composer' ) => [
						'value' => 'bounceOutRight',
						'type' => 'out',
					],
					__( 'bounceOutUp', 'js_composer' ) => [
						'value' => 'bounceOutUp',
						'type' => 'out',
					],
				],
			],
			[
				'label' => __( 'Fading Entrances', 'js_composer' ),
				'values' => [
					// text to display => value.
					__( 'fadeIn', 'js_composer' ) => [
						'value' => 'fadeIn',
						'type' => 'in',
					],
					__( 'fadeInDown', 'js_composer' ) => [
						'value' => 'fadeInDown',
						'type' => 'in',
					],
					__( 'fadeInDownBig', 'js_composer' ) => [
						'value' => 'fadeInDownBig',
						'type' => 'in',
					],
					__( 'fadeInLeft', 'js_composer' ) => [
						'value' => 'fadeInLeft',
						'type' => 'in',
					],
					__( 'fadeInLeftBig', 'js_composer' ) => [
						'value' => 'fadeInLeftBig',
						'type' => 'in',
					],
					__( 'fadeInRight', 'js_composer' ) => [
						'value' => 'fadeInRight',
						'type' => 'in',
					],
					__( 'fadeInRightBig', 'js_composer' ) => [
						'value' => 'fadeInRightBig',
						'type' => 'in',
					],
					__( 'fadeInUp', 'js_composer' ) => [
						'value' => 'fadeInUp',
						'type' => 'in',
					],
					__( 'fadeInUpBig', 'js_composer' ) => [
						'value' => 'fadeInUpBig',
						'type' => 'in',
					],
				],
			],
			[
				'label' => __( 'Fading Exits', 'js_composer' ),
				'values' => [
					__( 'fadeOut', 'js_composer' ) => [
						'value' => 'fadeOut',
						'type' => 'out',
					],
					__( 'fadeOutDown', 'js_composer' ) => [
						'value' => 'fadeOutDown',
						'type' => 'out',
					],
					__( 'fadeOutDownBig', 'js_composer' ) => [
						'value' => 'fadeOutDownBig',
						'type' => 'out',
					],
					__( 'fadeOutLeft', 'js_composer' ) => [
						'value' => 'fadeOutLeft',
						'type' => 'out',
					],
					__( 'fadeOutLeftBig', 'js_composer' ) => [
						'value' => 'fadeOutLeftBig',
						'type' => 'out',
					],
					__( 'fadeOutRight', 'js_composer' ) => [
						'value' => 'fadeOutRight',
						'type' => 'out',
					],
					__( 'fadeOutRightBig', 'js_composer' ) => [
						'value' => 'fadeOutRightBig',
						'type' => 'out',
					],
					__( 'fadeOutUp', 'js_composer' ) => [
						'value' => 'fadeOutUp',
						'type' => 'out',
					],
					__( 'fadeOutUpBig', 'js_composer' ) => [
						'value' => 'fadeOutUpBig',
						'type' => 'out',
					],
				],
			],
			[
				'label' => __( 'Flippers', 'js_composer' ),
				'values' => [
					__( 'flip', 'js_composer' ) => [
						'value' => 'flip',
						'type' => 'other',
					],
					__( 'flipInX', 'js_composer' ) => [
						'value' => 'flipInX',
						'type' => 'in',
					],
					__( 'flipInY', 'js_composer' ) => [
						'value' => 'flipInY',
						'type' => 'in',
					],
					__( 'flipOutX', 'js_composer' ) => [
						'value' => 'flipOutX',
						'type' => 'out',
					],
					__( 'flipOutY', 'js_composer' ) => [
						'value' => 'flipOutY',
						'type' => 'out',
					],
				],
			],
			[
				'label' => __( 'Lightspeed', 'js_composer' ),
				'values' => [
					__( 'lightSpeedIn', 'js_composer' ) => [
						'value' => 'lightSpeedIn',
						'type' => 'in',
					],
					__( 'lightSpeedOut', 'js_composer' ) => [
						'value' => 'lightSpeedOut',
						'type' => 'out',
					],
				],
			],
			[
				'label' => __( 'Rotating Entrances', 'js_composer' ),
				'values' => [
					__( 'rotateIn', 'js_composer' ) => [
						'value' => 'rotateIn',
						'type' => 'in',
					],
					__( 'rotateInDownLeft', 'js_composer' ) => [
						'value' => 'rotateInDownLeft',
						'type' => 'in',
					],
					__( 'rotateInDownRight', 'js_composer' ) => [
						'value' => 'rotateInDownRight',
						'type' => 'in',
					],
					__( 'rotateInUpLeft', 'js_composer' ) => [
						'value' => 'rotateInUpLeft',
						'type' => 'in',
					],
					__( 'rotateInUpRight', 'js_composer' ) => [
						'value' => 'rotateInUpRight',
						'type' => 'in',
					],
				],
			],
			[
				'label' => __( 'Rotating Exits', 'js_composer' ),
				'values' => [
					__( 'rotateOut', 'js_composer' ) => [
						'value' => 'rotateOut',
						'type' => 'out',
					],
					__( 'rotateOutDownLeft', 'js_composer' ) => [
						'value' => 'rotateOutDownLeft',
						'type' => 'out',
					],
					__( 'rotateOutDownRight', 'js_composer' ) => [
						'value' => 'rotateOutDownRight',
						'type' => 'out',
					],
					__( 'rotateOutUpLeft', 'js_composer' ) => [
						'value' => 'rotateOutUpLeft',
						'type' => 'out',
					],
					__( 'rotateOutUpRight', 'js_composer' ) => [
						'value' => 'rotateOutUpRight',
						'type' => 'out',
					],
				],
			],
			[
				'label' => __( 'Specials', 'js_composer' ),
				'values' => [
					__( 'hinge', 'js_composer' ) => [
						'value' => 'hinge',
						'type' => 'out',
					],
					__( 'rollIn', 'js_composer' ) => [
						'value' => 'rollIn',
						'type' => 'in',
					],
					__( 'rollOut', 'js_composer' ) => [
						'value' => 'rollOut',
						'type' => 'out',
					],
				],
			],
			[
				'label' => __( 'Zoom Entrances', 'js_composer' ),
				'values' => [
					__( 'zoomIn', 'js_composer' ) => [
						'value' => 'zoomIn',
						'type' => 'in',
					],
					__( 'zoomInDown', 'js_composer' ) => [
						'value' => 'zoomInDown',
						'type' => 'in',
					],
					__( 'zoomInLeft', 'js_composer' ) => [
						'value' => 'zoomInLeft',
						'type' => 'in',
					],
					__( 'zoomInRight', 'js_composer' ) => [
						'value' => 'zoomInRight',
						'type' => 'in',
					],
					__( 'zoomInUp', 'js_composer' ) => [
						'value' => 'zoomInUp',
						'type' => 'in',
					],
				],
			],
			[
				'label' => __( 'Zoom Exits', 'js_composer' ),
				'values' => [
					__( 'zoomOut', 'js_composer' ) => [
						'value' => 'zoomOut',
						'type' => 'out',
					],
					__( 'zoomOutDown', 'js_composer' ) => [
						'value' => 'zoomOutDown',
						'type' => 'out',
					],
					__( 'zoomOutLeft', 'js_composer' ) => [
						'value' => 'zoomOutLeft',
						'type' => 'out',
					],
					__( 'zoomOutRight', 'js_composer' ) => [
						'value' => 'zoomOutRight',
						'type' => 'out',
					],
					__( 'zoomOutUp', 'js_composer' ) => [
						'value' => 'zoomOutUp',
						'type' => 'out',
					],
				],
			],
			[
				'label' => __( 'Slide Entrances', 'js_composer' ),
				'values' => [
					__( 'slideInDown', 'js_composer' ) => [
						'value' => 'slideInDown',
						'type' => 'in',
					],
					__( 'slideInLeft', 'js_composer' ) => [
						'value' => 'slideInLeft',
						'type' => 'in',
					],
					__( 'slideInRight', 'js_composer' ) => [
						'value' => 'slideInRight',
						'type' => 'in',
					],
					__( 'slideInUp', 'js_composer' ) => [
						'value' => 'slideInUp',
						'type' => 'in',
					],
				],
			],
			[
				'label' => __( 'Slide Exits', 'js_composer' ),
				'values' => [
					__( 'slideOutDown', 'js_composer' ) => [
						'value' => 'slideOutDown',
						'type' => 'out',
					],
					__( 'slideOutLeft', 'js_composer' ) => [
						'value' => 'slideOutLeft',
						'type' => 'out',
					],
					__( 'slideOutRight', 'js_composer' ) => [
						'value' => 'slideOutRight',
						'type' => 'out',
					],
					__( 'slideOutUp', 'js_composer' ) => [
						'value' => 'slideOutUp',
						'type' => 'out',
					],
				],
			],
		];

		/**
		 * Used to override animation style list
		 *
		 * @since 4.4
		 */

		return apply_filters( 'vc_param_animation_style_list', $styles );
	}

	/**
	 * Group styles by type.
	 *
	 * @param array $styles - array of styles to group.
	 * @param string|array $type - what type to return.
	 *
	 * @return array
	 * @since 4.4
	 */
	public function groupStyleByType( $styles, $type ) { // phpcs:ignore:CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		$grouped = [];
		foreach ( $styles as $group ) {
			$inner_group = [ 'values' => [] ];
			if ( isset( $group['label'] ) ) {
				$inner_group['label'] = $group['label'];
			}
			foreach ( $group['values'] as $key => $value ) {
				if ( ( is_array( $value ) && isset( $value['type'] ) && ( ( is_string( $type ) && $value['type'] === $type ) || is_array( $type ) && in_array( $value['type'], $type, true ) ) ) || ! is_array( $value ) || ! isset( $value['type'] ) ) {
					$inner_group['values'][ $key ] = $value;
				}
			}
			if ( ! empty( $inner_group['values'] ) ) {
				$grouped[] = $inner_group;
			}
		}

		return $grouped;
	}

	/**
	 * Set variables and register animate-css asset.
	 *
	 * @param array $settings
	 * @param string $value
	 * @since 4.4
	 */
	public function __construct( $settings, $value ) {
		$this->settings = $settings;
		$this->value = $value;
		wp_register_style( 'vc_animate-css', vc_asset_url( 'lib/vendor/dist/animate.css/animate.min.css' ), [], WPB_VC_VERSION );
	}

	/**
	 * Render edit form output.
	 *
	 * @param string $param_id since 9.0.
	 *
	 * @return string
	 * @since 4.4
	 */
	public function render( $param_id = '' ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh, CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		$output = '';
		wp_enqueue_style( 'vc_animate-css' );

		$styles = $this->animationStyles();
		if ( isset( $this->settings['settings']['type'] ) ) {
			$styles = $this->groupStyleByType( $styles, $this->settings['settings']['type'] );
		}
		if ( isset( $this->settings['settings']['custom'] ) && is_array( $this->settings['settings']['custom'] ) ) {
			$styles = array_merge( $styles, $this->settings['settings']['custom'] );
		}

		if ( is_array( $styles ) && ! empty( $styles ) ) {
			foreach ( $styles as $style_key => $style ) {
				if ( ! is_array( $style['values'] ) || empty( $style['values'] ) ) {
					continue;
				}
				foreach ( $style['values'] as $label => $value ) {
					$option_value = is_array( $value ) ? $value['value'] : $value;
					$selected = false;
					if ( $option_value === $this->value ) {
						$selected = true;
					}
					$styles[ $style_key ]['value'][] = [
						'value' => $option_value,
						'selected' => $selected,
						'label' => $label,
					];
				}
				unset( $styles[ $style_key ]['values'] );
			}

			$build_style_select = WPB_Form_Field_Dropdown::get( [
				'id' => wpbakery()->editForm()->get_value_control_id( $param_id, $this->settings['type'] ),
				'classes' => 'vc_param-animation-style wpb-form-select',
				'options' => $styles,
			] );

			$output .= $build_style_select;
		}

		$output .= WPB_Form_Field_Hidden::get([
			'name' => $this->settings['param_name'],
			'classes' => wpbakery()->editForm()->get_value_control_classes( $this->settings['param_name'], $this->settings['type'] ),
			'value' => $this->value,
			'is_value_escape' => false,
		]);

		return $output; // nosemgrep - we already escaped everything on this step.
	}
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered 'values'.
 *
 * @param array $settings - parameter settings in vc_map.
 * @param string $value - parameter value.
 * @param string $tag - shortcode tag.
 * @param string $param_id
 *
 * @see vc_filter: vc_animation_style_render_filter - filter to override editor form
 *     field output
 *
 * @return mixed rendered template for params in edit form
 *
 * @since 4.4
 */
function vc_animation_style_form_field( $settings, $value, $tag, $param_id ) {

	$field = new Vc_ParamAnimation( $settings, $value );

	/**
	 * Filter used to override full output of edit form field animation style
	 *
	 * @since 4.4
	 */

	return apply_filters( 'vc_animation_style_render_filter', $field->render( $param_id ), $settings, $value, $tag );
}
