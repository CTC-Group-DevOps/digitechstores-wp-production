<?php
/**
 * WPBakery Page Builder shortcode attributes fields
 *
 * @package WPBakeryPageBuilder
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Edit form fields builder for shortcode attributes.
 *
 * @since 4.4
 */
class Vc_Edit_Form_Fields {
	/**
	 * Shortcode tag used to identify the shortcode type.
	 *
	 * @since 4.4
	 * @var bool
	 */
	protected $tag = false;
	/**
	 * Array of attributes assigned to the shortcode.
	 *
	 * @since 4.4
	 * @var array
	 */
	protected $atts = [];
	/**
	 * Configuration settings for the shortcode.
	 *
	 * @since 4.4
	 * @var array
	 */
	protected $settings = [];
	/**
	 * Post ID.
	 *
	 * @since 4.4
	 * @var bool
	 */
	protected $post_id = false;

	/**
	 * Construct Form fields.
	 *
	 * @param string $tag - shortcode tag.
	 * @param array $atts - list of attribute assign to the shortcode.
	 * @param array $shortcode_atts - list current shortcode atts.
	 * @throws \Exception
	 * @since 4.4
	 */
	public function __construct( $tag, $atts, $shortcode_atts = [] ) {
		require_once vc_path_dir( 'MIGRATIONS_DIR', 'class-wpb-edit-form-attributes-migration.php' );
		$atts_migration = new Wpb_Edit_Form_Attributes_Migration();
		$atts_migration->init();
		$this->tag = $tag;
		$this->setSettings( WPBMap::getShortCode( $this->tag ) );
		$this->atts = apply_filters( 'vc_edit_form_fields_attributes_' . $this->tag, $atts, $this->settings, $shortcode_atts );
	}

	/**
	 * Get settings
	 *
	 * @param string $key
	 *
	 * @return null
	 * @since 4.4
	 */
	public function setting( $key ) {
		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : null;
	}

	/**
	 * Set settings data
	 *
	 * @param array $settings
	 * @since 4.4
	 */
	public function setSettings( array $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Shortcode Post ID getter.
	 * If post id isn't set try to get from get_the_ID function.
	 *
	 * @return int|bool;
	 * @since 4.4
	 */
	public function postId() {
		if ( ! $this->post_id ) {
			$this->post_id = get_the_ID();
		}

		return $this->post_id;
	}

	/**
	 * Shortcode Post ID setter.
	 *
	 * @param int $post_id - value in post_id.
	 * @since 4.4
	 */
	public function setPostId( $post_id ) {
		$this->post_id = (int) $post_id;
	}

	/**
	 * Get shortcode attribute value.
	 *
	 * This function checks if value isn't set then it uses std or value fields in param settings.
	 *
	 * @param array $param_settings
	 * @param mixed $value
	 *
	 * @since 4.4
	 * @return string
	 */
	protected function parseShortcodeAttributeValue( $param_settings, $value ) {
		if ( is_null( $value ) ) { // If value doesn't exists.
			if ( isset( $param_settings['std'] ) ) {
				$value = $param_settings['std'];
			} elseif ( isset( $param_settings['value'] ) && is_array( $param_settings['value'] ) && ! empty( $param_settings['type'] ) && 'checkbox' !== $param_settings['type'] ) {
				$first_key = key( $param_settings['value'] );
				$value = $first_key ? $param_settings['value'][ $first_key ] : '';
			} elseif ( isset( $param_settings['value'] ) && ! is_array( $param_settings['value'] ) ) {
				$value = $param_settings['value'];
			}
		} elseif ( 'css' == $param_settings['param_name'] && isset( $param_settings['value'] ) && '.vc_custom_' !== substr( $value, 0, 11 ) ) {
			// check if string value is default or modified (modified starts with a class name .vc_custom_[timestamp]).
			$css_values = $param_settings['value'];
			$value = wp_json_encode( $css_values );
		}

		return $value;
	}

	/**
	 * Enqueue js scripts for attributes types.
	 *
	 * @param array|null $params - since 8.7.
	 * @return string
	 * @since 4.4
	 */
	public function enqueueScripts( $params = [] ) {
		$params = is_array( $params ) ? $params : [];
		$param_type_list = $this->get_param_types_list( $params );

		$output = '';
		$scripts = apply_filters( 'vc_edit_form_enqueue_script', WpbakeryShortcodeParams::getScripts() );
		if ( ! is_array( $scripts ) ) {
			return $output;
		}

		foreach ( $scripts as $param_type => $script ) {
			$is_edit_form_script = strpos( $script, 'edit-form' ) !== false;
			$is_element_param_type = in_array( $param_type, $param_type_list, true );
			if ( ! $is_element_param_type && ! $is_edit_form_script ) {
				continue;
			}

			$custom_tag = 'script';
			$output .= '<' . $custom_tag . ' src="' . esc_url( $script ) . '"></' . $custom_tag . '>';
		}

		return $output;
	}

	/**
	 * Get list of unique parameter types from element setting.
	 *
	 * @since 8.7
	 * @param array $settings
	 * @return array
	 */
	public function get_param_types_list( array $settings ) {
		$types = [];

		foreach ( $settings as $single_param ) {
			if ( ! isset( $single_param['type'] ) ) {
				continue;
			} else {
				$types[] = $single_param['type'];
			}

			if ( 'param_group' === $single_param['type'] ) {
				$types = array_merge( $types, $this->get_param_types_list( $single_param['params'] ) );
			}
		}

		return array_values( array_unique( $types ) );
	}

	/**
	 * Render grouped fields.
	 *
	 * @param array $groups
	 * @param array $group_list
	 *
	 * @return string
	 * @since 4.4
	 */
	protected function renderGroupedFields( $groups, $group_list ) {
		$output = '';
		if ( count( $groups ) > 1 || ( count( $groups ) >= 1 && empty( $group_list['_general'] ) ) ) {
			$output .= '<div class="vc_panel-tabs" id="vc_edit-form-tabs">';
			$output .= '<ul class="vc_general vc_ui-tabs-line" data-vc-ui-element="panel-tabs-controls" role="tablist" aria-label="' . esc_html__( 'Edit Element Tabs', 'js_composer' ) . '">';
			$key = 0;
			foreach ( $groups as $group_name ) {
				$output .= '<li class="vc_edit-form-tab-control" data-tab-index="' . esc_attr( $key ) . '" role="presentation"><button data-vc-ui-element-target="#vc_edit-form-tab-' . ( $key++ ) . '" class="vc_ui-tabs-line-trigger" data-vc-ui-element="panel-tab-control" role="tab">' . ( '_general' === $group_name ? esc_html__( 'General', 'js_composer' ) : $group_name ) . '</button></li>';
			}
			$output .= vc_get_template( 'editors/popups/partials/more-tabs-button.php' );
			$output .= '</ul>';

			$key = 0;
			foreach ( $groups as $group_name ) {
				$output .= '<form id="vc_edit-form-tab-' . ( $key++ ) . '" class="vc_edit-form-tab vc_row vc_ui-flex-row" data-vc-ui-element="panel-edit-element-tab">';

				$output .= $this->get_group_output( $group_list, $group_name );
				$output .= '</form>';
			}
			$output .= '</div>';
		} elseif ( ! empty( $group_list['_general'] ) ) {
			$output .= '<form class="vc_edit-form-tab vc_row vc_ui-flex-row vc_active" data-vc-ui-element="panel-edit-element-tab">' . $this->get_group_output( $group_list, '_general' ) . '</form>';
		}

		return $output;
	}

	/**
	 * Get group output.
	 *
	 * @since 9.0
	 * @param array $group_list
	 * @param string $group_name
	 * @return string
	 */
	public function get_group_output( $group_list, $group_name ) {
		$output = '';
		foreach ( $group_list[ $group_name ] as $param ) {
			$name = isset( $param['param_name'] ) ? $param['param_name'] : null;
			if ( is_null( $name ) ) {
				continue;
			}
			$value = isset( $this->atts[ $name ] ) ? $this->atts[ $name ] : null;
			$value = $this->parseShortcodeAttributeValue( $param, $value );
			$output .= $this->renderField( $param, $value );
		}

		return $output;
	}

	/**
	 * Render fields html and output it.
	 *
	 * @since 4.4
	 * vc_filter: vc_edit_form_class - filter to override editor_css_classes array
	 */
	public function render() {
		$this->loadDefaultParams();
		$output = $el_position = '';
		$params = $this->setting( 'params' );
		$editor_css_classes = apply_filters( 'vc_edit_form_class', [
			'wpb_edit_form_elements',
			'vc_edit_form_elements',
		], $this->atts, $params );
		$deprecated = $this->setting( 'deprecated' );
		require_once vc_path_dir( 'CORE_DIR', 'presets/class-vc-settings-presets.php' );
		$show_settings = false;

		$save_as_template_elements = apply_filters( 'vc_popup_save_as_template_elements', [
			'vc_row',
			'vc_section',
		] );

		$show_presets = ! in_array( $this->tag, $save_as_template_elements, true ) && vc_user_access()->part( 'presets' )->checkStateAny( true, null )->get();

		if ( in_array( $this->tag, $save_as_template_elements, true ) && vc_user_access()->part( 'templates' )->checkStateAny( true, null )->get() ) {
			$show_settings = true;
		}
		$custom_tag = 'script';
		$output .= sprintf( '<' . $custom_tag . '>window.vc_presets_show=%s;</' . $custom_tag . '>', $show_presets ? 'true' : 'false' );
		$output .= sprintf( '<' . $custom_tag . '>window.vc_settings_show=%s;</' . $custom_tag . '>', $show_presets || $show_settings ? 'true' : 'false' );

		if ( $deprecated ) {
			$output .= '<div class="vc_row vc_ui-flex-row vc_shortcode-edit-form-deprecated-message"><div class="vc_col-sm-12 wpb_element_wrapper">' . vc_message_warning( sprintf( esc_html__( 'You are using outdated element, it is deprecated since version %s.', 'js_composer' ), $this->setting( 'deprecated' ) ) ) . '</div></div>';
		}
		$output .= '<div class="' . implode( ' ', $editor_css_classes ) . '" data-title="' . esc_attr__( 'Edit', 'js_composer' ) . ' ' . esc_attr( $this->setting( 'name' ) ) . '">';
		$group_list = $this->get_group_param_list( $params );
		$output .= $this->renderGroupedFields( array_keys( $group_list ), $group_list );
		$output .= '</div>';
		$output .= $this->enqueueScripts( $params );

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $output;
		do_action( 'vc_edit_form_fields_after_render' );
	}

	/**
	 * Get group param list
	 *
	 * @since 9.0
	 * @param mixed $params
	 * @return array
	 */
	public function get_group_param_list( $params ) {
		$list = [];

		if ( ! is_array( $params ) ) {
			return $list;
		}

		foreach ( $params as $param ) {
			$group = isset( $param['group'] ) && '' !== $param['group'] ? $param['group'] : '_general';
			$list[ $group ][] = $param;
		}

		return $this->divide_grouped_params_to_sections( $list );
	}

	/**
	 * We divide here grouped params to section according to 'section' param in each param settings.
	 *
	 * @since 9.0
	 * @param array $grouped_params
	 * @return array
	 */
	public function divide_grouped_params_to_sections( $grouped_params ) {
		foreach ( $grouped_params as $group_name => $params ) {
			$params = $this->divide_params_to_sections_in_group( $params );
			$params  = $this->sort_sections_in_group( $params );
			$params = $this->add_delimiter_section_params( $params );
			$params = $this->merge_sections_back_to_params( $params );
			$grouped_params[ $group_name ] = $params;
		}

		return $grouped_params;
	}

	/**
	 * Divide params to sections in group.
	 *
	 * @since 9.0
	 * @param array $params
	 * @return array
	 */
	public function divide_params_to_sections_in_group( $params ) {
		$grouped_sections_params = [];
		$params_without_section = [];
		foreach ( $params as $single_param ) {
			if ( empty( $single_param['section'] ) || ! is_string( $single_param['section'] ) ) {
				$params_without_section[] = $single_param;
				continue;
			}

			$grouped_sections_params[ $single_param['section'] ][] = $single_param;
		}

		// if we gave at least one section in group all params without section should go to the general section.
		if ( $grouped_sections_params && $params_without_section ) {
			$general_section_params = $this->add_general_section_to_params( $params_without_section );
			$grouped_sections_params[ vc_config()->get_general_section_slug() ] = $general_section_params;
		} else {
			$grouped_sections_params = array_merge( $grouped_sections_params, $params_without_section );
		}

		return $grouped_sections_params;
	}

	/**
	 * Add params without sections to the general section.
	 *
	 * @since 9.0
	 * @param array $params
	 *
	 * @return array
	 */
	public function add_general_section_to_params( $params ) {

		foreach ( $params as $key => $single_param ) {
			$params[ $key ]['section'] = vc_config()->get_general_section_slug();
		}

		return $params;
	}

	/**
	 * Merge sections back to params.
	 * We divided params to section in divide_params_to_sections_in_group right now we need merge them back.
	 *
	 * @since 9.0
	 * @param array $params
	 * @return array
	 */
	public function merge_sections_back_to_params( $params ) {
		$result = [];
		foreach ( $params as $section_name => $section_data ) {
			if ( is_string( $section_name ) ) {
				if ( ! is_array( $section_data ) ) {
					continue;
				}

				foreach ( $section_data as $section_param ) {
					$result[] = $section_param;
				}
			} else {
				$result[] = $section_data;
			}
		}

		return $result;
	}

	/**
	 * Sort sections in group according to 'sections' setting in shortcode settings.
	 *
	 * @since 9.0
	 * @param array $params
	 * @return array
	 */
	public function sort_sections_in_group( $params ) {
		$how_sort_sections = $this->setting( 'sections' );
		if ( ! is_array( $how_sort_sections ) || [] === $how_sort_sections ) {
			return $params;
		}

		foreach ( array_reverse( $how_sort_sections ) as $section_slug ) {
			if ( empty( $params[ $section_slug ] ) ) {
				continue;
			}

			$section_data = $params[ $section_slug ];
			unset( $params[ $section_slug ] );
			$params = array_merge( [ $section_slug => $section_data ], $params );
		}

		return $params;
	}

	/**
	 * Add delimiter to last param in each section.
	 *
	 * @since 9.0
	 * @param array $params
	 * @return array
	 */
	public function add_delimiter_section_params( $params ) {
		foreach ( $params as $section_slug => $section_data ) {
			// if not a string than it's not a section.
			if ( ! is_string( $section_slug ) ) {
				continue;
			}

			$last_param_key = array_key_last( $section_data );
			$first_param_key = array_key_first( $section_data );

			if ( $this->is_exceptional_section_case( $section_data ) ) {
				continue;
			}

			$params[ $section_slug ][ $first_param_key ]['wpb_param_section_start'] = true;
			$params[ $section_slug ][ $last_param_key ]['wpb_param_section_end'] = true;
		}

		return $params;
	}

	/**
	 * Check some edge cases when we do not process the section.
	 *
	 * @since 9.0
	 * @param array $section_data
	 * @return bool
	 */
	public function is_exceptional_section_case( $section_data ) {
		if ( $this->is_section_has_only_hidden_params( $section_data ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if section has only hidden param types.
	 *
	 * @since 9.0
	 * @param array $section_data
	 * @return bool
	 */
	public function is_section_has_only_hidden_params( $section_data ) {
		foreach ( $section_data as $param ) {
			if ( isset( $param['type'] ) && 'hidden' !== $param['type'] ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Generate html for shortcode attribute.
	 *
	 * @see vc_filter: vc_single_param_edit - hook to edit any shortode param
	 * @see vc_filter: vc_form_fields_render_field_{shortcode_name}_{param_name}_param_value - hook to edit shortcode param
	 *     value vc_filter: vc_form_fields_render_field_{shortcode_name}_{param_name}_param - hook to edit shortcode
	 *     param attributes vc_filter: vc_single_param_edit_holder_output - hook to edit output of this method
	 *
	 * @param array $param
	 * @param string $param_id since 9.0.
	 * @return mixed
	 * @since 4.4
	 */
	public function handleHeading( $param, $param_id = '' ) {

		return vc_get_template('editors/partials/param-heading.tpl.php', [
			'param'    => $param,
			'param_id' => $param_id,
		] );
	}

	/**
	 * Render field.
	 *
	 * @param array $param
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function renderField( $param, $value ) {
		$param['vc_single_param_edit_holder_class'] = [
			'wpb_el_type_' . $param['type'],
			'vc_wrapper-param-type-' . $param['type'],
			'vc_shortcode-param',
			'vc_column',
		];

		if ( ! empty( $param['param_holder_class'] ) ) {
			$param['vc_single_param_edit_holder_class'][] = $param['param_holder_class'];
		}

		$output = '';
		$param_id = uniqid();
		$param = apply_filters( 'vc_single_param_edit', $param, $value );
		$output .= '<div class="' . implode( ' ', $param['vc_single_param_edit_holder_class'] ) . '" data-vc-ui-element="panel-shortcode-param" data-vc-shortcode-param-name="' . esc_attr( $param['param_name'] ) . '" data-param_type="' . esc_attr( $param['type'] ) . '" data-param_settings="' . htmlentities( wp_json_encode( $param ) ) . '">';
		$output .= $this->handleHeading( $param, $param_id );

		$output .= '<div class="edit_form_line">';
		$value = apply_filters( 'vc_form_fields_render_field_' . $this->setting( 'base' ) . '_' . $param['param_name'] . '_param_value', $value, $param, $this->settings, $this->atts );
		$param = apply_filters( 'vc_form_fields_render_field_' . $this->setting( 'base' ) . '_' . $param['param_name'] . '_param', $param, $value, $this->settings, $this->atts );
		$output = apply_filters( 'vc_edit_form_fields_render_field_' . $param['type'] . '_before', $output );

		if ( ! empty( $param['wpb_param_section_start'] ) ) {
			$output = '<div class="wpb-modal-section">' . $output;
		}

		$output .= vc_do_shortcode_param_settings_field( $param['type'], $param, $value, $this->setting( 'base' ), $param_id );
		$output_after = '';
		$output_after .= '</div>'; // .edit_form_line
		$output_after .= '</div>'; // .vc_shortcode-param
		$output .= apply_filters( 'vc_edit_form_fields_render_field_' . $param['type'] . '_after', $output_after );
		if ( ! empty( $param['wpb_param_section_end'] ) ) {
			$output .= '</div>';
		}

		return apply_filters( 'vc_single_param_edit_holder_output', $output, $param, $value, $this->settings, $this->atts );
	}

	/**
	 * Create default shortcode params
	 *
	 * List of params stored in global variable $vc_params_list.
	 * Please check include/params/load.php for default params list.
	 *
	 * @return bool
	 * @since 4.4
	 */
	public function loadDefaultParams() {
		global $vc_params_list;
		if ( empty( $vc_params_list ) ) {
			return false;
		}
		foreach ( $vc_params_list as $param ) {
			vc_add_shortcode_param( $param, 'vc_' . $param . '_form_field', null );
		}
		do_action( 'vc_load_default_params' );

		return true;
	}
}
