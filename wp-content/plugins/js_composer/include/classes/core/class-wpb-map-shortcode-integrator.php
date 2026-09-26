<?php
/**
 * Help integrate shortcodes.
 * We call integration process when we need one element shortcode param inside another.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WpbMapShortcodeIntegrator class.
 *
 * @since 9.0
 */
class WpbMapShortcodeIntegrator {
	/**
	 * Get element shortcode map with custom parameters.
	 *
	 * @since 9.0
	 * @param array $args
	 * @return array
	 * @throws Exception
	 */
	public function integrate_shortcode( array $args ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh, CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		$args = array_merge(
			[
				'shortcode'                    => '',
				'field_prefix'                 => '',
				'group_prefix'                 => '',
				'change_fields'                => null,
				'dependency'                   => null,
				'is_sections'                  => false,
				'is_remove_css_animation_group' => true,
			],
			$args
		);

		$shortcode                    = $args['shortcode'];
		$field_prefix                 = $args['field_prefix'];
		$group_prefix                 = $args['group_prefix'];
		$change_fields                = $args['change_fields'];
		$dependency                   = $args['dependency'];
		$is_sections                  = $args['is_sections'];
		$is_remove_css_animation_group = $args['is_remove_css_animation_group'];

		$shortcode_data = is_string( $shortcode ) ? WPBMap::getShortCode( $shortcode ) : $shortcode;
		if ( empty( $shortcode_data['params'] ) || ! is_array( $shortcode_data['params'] ) ) {
			return [];
		}

		// WPBakeryShortCodeFishBones $shortcode - base shortcode.
		$params = $shortcode_data['params'];

		$keys = array_keys( $params );
		$count = count( $keys );
		for ( $i = 0; $i < $count; $i++ ) {
			$param = &$params[ $keys[ $i ] ]; // Note! passed by reference to automatically update data.
			if ( isset( $change_fields ) ) {
				$param = $this->integrate_include_exclude_fields( $param, $change_fields );
				if ( empty( $param ) ) {
					continue;
				}
			}
			if ( ! empty( $group_prefix ) ) {
				if ( $is_remove_css_animation_group ) {
					if ( isset( $param['group'] ) && 'css_animation' === $param['param_name'] ) {
						unset( $param['group'] );
					}
				}

				$param['group'] = isset( $param['group'] )
					? $group_prefix . ': ' . $param['group']
					: $group_prefix;
			}

			if ( isset( $param['section'] ) && ! $is_sections ) {
				unset( $param['section'] );
			}

			if ( ! empty( $field_prefix ) && isset( $param['param_name'] ) ) {
				$param['param_name'] = $field_prefix . $param['param_name'];
				if ( isset( $param['dependency']['element'] ) && is_array( $param['dependency'] ) ) {
					$param['dependency']['element'] = $field_prefix . $param['dependency']['element'];
				}
				$param = $this->add_dependency( $param, $dependency );

			} elseif ( ! empty( $dependency ) ) {
				$param = $this->add_dependency( $param, $dependency );
			}
			$param['integrated_shortcode'] = is_array( $shortcode ) ? $shortcode['base'] : $shortcode;
			$param['integrated_shortcode_field'] = $field_prefix;
		}

		return is_array( $params ) ? array_filter( $params ) : [];
	}

	/**
	 * Get element shortcode map include/exclude some param fields.
	 *
	 * @param array $param
	 * @param array $change_fields
	 *
	 * @return array|null
	 * @since 9.0
	 */
	public function integrate_include_exclude_fields( $param, $change_fields ) {
		if ( ! is_array( $change_fields ) || ! isset( $param['param_name'] ) ) {
			return $param;
		}
		$param_name = $param['param_name'];

		if ( isset( $change_fields['exclude'] ) ) {
			$param = in_array( $param_name, $change_fields['exclude'], true ) ? null : $param;
		} elseif ( isset( $change_fields['exclude_regex'] ) ) {
			$param = $this->check_param_field_against_regex( $param, $change_fields['exclude_regex'], 'exclude' );
		}

		if ( isset( $change_fields['include_only'] ) ) {
			$param = ! in_array( $param_name, $change_fields['include_only'], true ) ? null : $param;
		} elseif ( isset( $change_fields['include_only_regex'] ) ) {
			$param = $this->check_param_field_against_regex( $param, $change_fields['include_only_regex'], 'include' );
		}

		return $param;
	}

	/**
	 * Check shortcode param against regex.
	 *
	 * @param array $param
	 * @param string|array $regex_list
	 * @param string $condition
	 *
	 * @since 9.0
	 * @return array|null
	 */
	public function check_param_field_against_regex( $param, $regex_list, $condition ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh, CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		$check_against = 'exclude' === $condition ? 1 : 0;

		if ( is_array( $regex_list ) && ! empty( $regex_list ) ) {
			$break_foreach = false;

			foreach ( $regex_list as $regex ) {
				if ( wpb_is_regex_valid( $regex ) ) {
					if ( preg_match( $regex, $param['param_name'] ) === $check_against ) {
						$param = null;
						$break_foreach = true;
					}
				}
				if ( $break_foreach ) {
					break;
				}
			}
			if ( $break_foreach ) {
				return $param; // to prevent group adding to $param.
			}
		} elseif ( is_string( $regex_list ) && strlen( $regex_list ) > 0 ) {
			$regex = $regex_list;
			if ( wpb_is_regex_valid( $regex ) ) {
				if ( preg_match( $regex, $param['param_name'] ) === $check_against ) {
					return null; // to prevent group adding to $param.
				}
			}
		}

		return $param;
	}

	/**
	 * Adds a dependency to a parameter if it does not already have one.
	 *
	 * @param array $param
	 * @param mixed $dependency
	 *
	 * @return array
	 * @since 9.0
	 */
	public function add_dependency( $param, $dependency ) {
		// activator must be used for all elements if they do not have 'dependency'.
		if ( ! empty( $dependency ) && empty( $param['dependency'] ) ) {
			if ( is_array( $dependency ) ) {
				$param['dependency'] = $dependency;
			}
		}

		return $param;
	}

	/**
	 * Retrieves parameters of a given base shortcode that are associated with a specified integrated shortcode.
	 *
	 * @param string $base_shortcode
	 * @param string $integrated_shortcode
	 * @param string $field_prefix
	 * @return array
	 * @throws Exception
	 * @since 9.0
	 */
	public function get_params( $base_shortcode, $integrated_shortcode, $field_prefix = '' ) { // phpcs:ignore:CognitiveComplexity.Complexity.MaximumComplexity.TooHigh
		$shortcode_data = WPBMap::getShortCode( $base_shortcode );
		$params = [];
		if ( is_array( $shortcode_data ) && is_array( $shortcode_data['params'] ) && ! empty( $shortcode_data['params'] ) ) {
			foreach ( $shortcode_data['params'] as $param ) {
				if ( is_array( $param ) && isset( $param['integrated_shortcode'] ) && $integrated_shortcode === $param['integrated_shortcode'] ) {
					if ( ! empty( $field_prefix ) ) {
						if ( isset( $param['integrated_shortcode_field'] ) && $field_prefix === $param['integrated_shortcode_field'] ) {
							$params[] = $param;
						}
					} else {
						$params[] = $param;
					}
				}
			}
		}

		return $params;
	}
}
