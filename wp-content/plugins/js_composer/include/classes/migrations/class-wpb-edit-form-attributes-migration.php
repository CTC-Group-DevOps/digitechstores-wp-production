<?php
/**
 * Edit form attributes migration class.
 * We use it when changing attributes in our configurations
 * and want to provide backward compatibility of it for our edit modal.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'MIGRATIONS_DIR', 'abstract-class-wpb-attributes-migration.php' );

/**
 * Class Wpb_Edit_Form_Attributes_Migration
 *
 * @since 9.0
 */
class Wpb_Edit_Form_Attributes_Migration extends Wpb_Attributes_Migration_Abstract {
	/**
	 * Init hooks.
	 *
	 * @since 9.0
	 */
	public function init() {
		foreach ( [ 'vc_basic_grid', 'vc_masonry_grid', 'vc_media_grid', 'vc_masonry_media_grid' ] as $grid_base ) {
			add_filter( 'vc_edit_form_fields_attributes_' . $grid_base, [ $this, 'convert_grid_element_width_to_items_per_row' ] );
			add_filter( 'vc_edit_form_fields_attributes_' . $grid_base, [ $this, 'normalize_grid_gap_to_px' ] );
			add_filter( 'vc_edit_form_fields_attributes_' . $grid_base, [ $this, 'convert_grid_filter_color_to_custom' ] );
			add_filter( 'vc_edit_form_fields_attributes_' . $grid_base, [ $this, 'convert_grid_arrows_color_to_custom' ] );
			add_filter( 'vc_edit_form_fields_attributes_' . $grid_base, [ $this, 'convert_grid_paging_color_to_custom' ] );
			add_filter( 'vc_edit_form_fields_attributes_' . $grid_base, [ $this, 'convert_integrated_btn' ] );
		}

		VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Pie' );
		add_filter( 'vc_edit_form_fields_attributes_vc_pie', [
			'WPBakeryShortCode_Vc_Pie',
			'convertOldColorsToNew',
		] );

		VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Progress_Bar' );
		add_filter( 'vc_edit_form_fields_attributes_vc_progress_bar', [
			'WPBakeryShortCode_Vc_Progress_Bar',
			'convertAttributesToNewProgressBar',
		] );

		VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Wp_Text' );
		add_filter( 'vc_edit_form_fields_attributes_vc_wp_text', [
			'WPBakeryShortCode_Vc_Wp_Text',
			'convertTextAttributeToContent',
		] );

		VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Message' );
		add_filter( 'vc_edit_form_fields_attributes_vc_message', [
			'WPBakeryShortCode_Vc_Message',
			'convertAttributesToMessageBox2',
		] );

		add_filter( 'vc_edit_form_fields_attributes_vc_tweetmeme', [ $this, 'convert_tweetmeme_legacy_toggle_values' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_single_image', [ $this, 'convert_single_image_old_link_to_new' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_single_image', [ $this, 'convert_vc_single_image_dropdown_color_to_custom' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_separator', [ $this, 'convert_separator_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_text_separator', [ $this, 'convert_separator_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_zigzag', [ $this, 'convert_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_icon', [ $this, 'convert_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_icon', [ $this, 'convert_background_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_text_separator', [ $this, 'convert_icon_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_text_separator', [ $this, 'convert_icon_background_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_icon_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_icon_background_dropdown_color_to_custom' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_gitem_post_title', [ $this, 'convert_gitem_color_from_block_container_to_colorpicker' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_gitem_post_excerpt', [ $this, 'convert_gitem_color_from_block_container_to_colorpicker' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_gitem_post_author', [ $this, 'convert_gitem_color_from_block_container_to_colorpicker' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_gitem_post_date', [ $this, 'convert_gitem_color_from_block_container_to_colorpicker' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_gitem_post_title', [ $this, 'convert_gitem_text_align_from_font_container' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_gitem_post_author', [ $this, 'convert_gitem_text_align_from_font_container' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_gallery', [ $this, 'convert_gallery_old_custom_link_to_new' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_gitem_post_categories', [ $this, 'convert_category_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_gitem_image', [ $this, 'convert_border_dropdown_color_to_custom' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_posts_slider', [ $this, 'convert_posts_slider_count_textfield_to_number' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_btn', [ $this, 'convert_btn_default_outline_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_btn', [ $this, 'convert_btn_default_3d_dropdown_color_to_custom' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_btn', [ $this, 'convert_btn_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_btn', [ $this, 'convert_btn_outline_dropdown_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_btn', [ $this, 'convert_btn_3d_dropdown_color_to_custom' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_btn', [ $this, 'convert_btn_gradient_style_to_gradient_custom' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_progress_bar', [ $this, 'convert_progress_bar_bgcolor_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_progress_bar', [ $this, 'convert_progress_bar_values_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_progress_bar', [ $this, 'convert_progress_bar_options_checkbox_to_toggles' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_images_carousel', [ $this, 'convert_images_carousel_speed_to_numeric' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_round_chart', [ $this, 'convert_round_chart_stroke_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_round_chart', [ $this, 'convert_round_chart_legend_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_round_chart', [ $this, 'convert_round_chart_values_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_round_chart', [ $this, 'migrate_round_chart_style_custom_to_flat' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_line_chart', [ $this, 'convert_line_chart_values_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_line_chart', [ $this, 'migrate_line_chart_style_custom_to_flat' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_pie', [ $this, 'convert_pie_chart_color_to_custom' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_hoverbox', [ $this, 'convert_hoverbox_background_color_to_custom' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_pricing_table', [ $this, 'convert_integrated_btn' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_hoverbox', [ $this, 'convert_vc_hoverbox_integrated_btn' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_wp_archives', [ $this, 'convert_wp_archives_options_to_type_and_count' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_wp_rss', [ $this, 'convert_wp_rss_options_checkbox_to_toggles' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_tta_tabs', [ $this, 'convert_tta_tabs_pagination_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_tabs', [ $this, 'convert_tta_tabs_color_to_custom' ] );
		// Must run AFTER `convert_tta_tabs_color_to_custom` so we can mirror the resolved hex value.
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_tabs', [ $this, 'convert_tta_tabs_active_color_to_color' ], 11 );

		add_filter( 'vc_edit_form_fields_attributes_vc_tta_tabs', [ $this, 'convert_tta_no_fill_to_fill_content_area' ] );

		// vc_tta_tour shares the same color/active_color/no-fill schema as vc_tta_tabs.
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_tour', [ $this, 'convert_tta_tabs_pagination_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_tour', [ $this, 'convert_tta_tabs_color_to_custom' ] );
		// Must run AFTER `convert_tta_tabs_color_to_custom` so we can mirror the resolved hex value.
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_tour', [ $this, 'convert_tta_tabs_active_color_to_color' ], 11 );

		add_filter( 'vc_edit_form_fields_attributes_vc_tta_tour', [ $this, 'convert_tta_no_fill_to_fill_content_area' ] );

		// vc_tta_accordion shares the same color/active_color/fill schema as vc_tta_tabs.
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_accordion', [ $this, 'convert_tta_tabs_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_accordion', [ $this, 'convert_tta_tabs_active_color_to_color' ], 11 );
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_accordion', [ $this, 'convert_tta_title_colors_to_contrast' ], 12 );
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_accordion', [ $this, 'convert_tta_accordion_outline_color' ], 13 );

		add_filter( 'vc_edit_form_fields_attributes_vc_tta_accordion', [ $this, 'convert_tta_no_fill_to_fill_content_area' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_tta_pageable', [ $this, 'convert_tta_tabs_pagination_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_tta_pageable', [ $this, 'update_tta_pageable_autoplay_default' ], 10, 3 );

		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_cta_classic_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_cta_flat_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_cta_3d_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_cta_outline_color_to_custom' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_cta_el_width_dropdown_to_range' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_cta_add_button_dropdown_to_toggle' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_cta_add_icon_dropdown_to_toggle' ] );
		add_filter( 'vc_edit_form_fields_attributes_vc_cta', [ $this, 'convert_integrated_btn' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_section', [ $this, 'convert_section_content_placement_to_vertical_content_position' ] );

		add_filter( 'vc_edit_form_fields_attributes_vc_toggle', [ $this, 'convert_vc_toggle_dropdown_color_to_custom' ] );
	}

	/**
	 * Backward compatibility for the renamed `no_fill_content_area` param.
	 *
	 * See {@see Wpb_Template_Attributes_Migration::convert_tta_no_fill_to_fill_content_area()}
	 * for full details. This version runs on the edit form attributes so the new
	 * toggle reflects the value derived from any legacy saved data.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_no_fill_to_fill_content_area( $atts ) {
		if ( array_key_exists( 'fill_content_area', $atts ) ) {
			return $atts;
		}

		if ( array_key_exists( 'no_fill_content_area', $atts ) ) {
			$atts['fill_content_area'] = ( 'true' === $atts['no_fill_content_area'] ) ? '' : 'true';
		} elseif ( array_key_exists( 'no_fill', $atts ) ) {
			// vc_tta_accordion used the `no_fill` param before the rename.
			$atts['fill_content_area'] = ( 'true' === $atts['no_fill'] ) ? '' : 'true';
		} else {
			$atts['fill_content_area'] = 'true';
		}

		return $atts;
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_border_dropdown_color_to_custom( $atts ) {
		return $this->migrate_dropdown_color_to_custom( $atts, $atts, 'border_color', 'custom_border_color', true );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_vc_single_image_dropdown_color_to_custom( $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $atts, 'border_color' );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_vc_toggle_dropdown_color_to_custom( $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $atts, 'color' );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_category_dropdown_color_to_custom( $atts ) {
		return $this->migrate_dropdown_color_to_custom( $atts, $atts, 'category_color', 'custom_category_color', true );
	}

	/**
	 * X Button (tweetmeme) backward compatibility for legacy checkbox-based toggle attributes.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_tweetmeme_legacy_toggle_values( $atts ) {
		$map = [
			'mention_no_default'    => [
				'on'          => 'add',
				'value_field' => 'mention_custom_tweet_text',
			],
			'hashtag_no_default'    => [
				'on'          => 'add',
				'value_field' => 'hashtag_custom_tweet_text',
			],
			'hashtag_no_url'        => [
				'on'          => 'add',
				'value_field' => 'hashtag_custom_tweet_url',
			],
			'share_use_page_url'    => [
				'on'          => 'use_custom',
				'value_field' => 'share_use_custom_url',
			],
			'share_text_page_title' => [
				'on'          => 'use_custom',
				'value_field' => 'share_text_custom_text',
			],
		];
		foreach ( $map as $legacy_param => $config ) {
			if ( isset( $atts[ $legacy_param ] ) && '' === $atts[ $legacy_param ] && ! empty( $atts[ $config['value_field'] ] ) ) {
				$atts[ $legacy_param ] = $config['on'];
			}
		}
		return $atts;
	}

	/**
	 * Single image element backward compatibility
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_single_image_old_link_to_new( $atts ) {
		if ( empty( $atts['onclick'] ) && isset( $atts['img_link_large'] ) && 'yes' === $atts['img_link_large'] ) {
			$atts['onclick'] = 'img_link_large';
			unset( $atts['img_link_large'] );
		} elseif ( empty( $atts['onclick'] ) && ( ! isset( $atts['img_link_large'] ) || 'yes' !== $atts['img_link_large'] ) ) {
			unset( $atts['img_link_large'] );
		}

		if ( empty( $atts['onclick'] ) && ! empty( $atts['link'] ) ) {
			$atts['onclick'] = 'custom_link';
		}

		// since 9.0 image can have a link in image field.
		if ( isset( $atts['source'] ) && 'media_library' === $atts['source'] && ! empty( $atts['link'] ) ) {
			$atts['image'] = wp_json_encode([
				$atts['image'] => [
					'url' => $atts['link'],
					'target' => empty( $atts['img_link_target'] ) ? '_self' : $atts['img_link_target'],
				],
			]);
		}

		return $atts;
	}

	/**
	 * Check whether a dropdown color attribute should be migrated to a custom colorpicker attribute.
	 *
	 * Returns true only when the target is still empty, the source is present, and the source
	 * value is not already 'custom' (which signals the user already chose a colorpicker value).
	 *
	 * @param array  $atts
	 * @param string $source_key
	 * @param string $target_key
	 * @return bool
	 * @since 9.0
	 */
	protected function should_migrate_dropdown_color( array $atts, string $source_key, string $target_key ): bool {
		return ! empty( $atts[ $source_key ] ) && 'custom' !== $atts[ $source_key ];
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_separator_dropdown_color_to_custom( $atts ) {
		return $this->migrate_dropdown_color_to_custom( $atts, $atts, 'color', 'accent_color', true );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_dropdown_color_to_custom( $atts ) {
		return $this->migrate_dropdown_color_to_custom( $atts, $atts, 'color', 'custom_color', true );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_background_dropdown_color_to_custom( $atts ) {
		return $this->migrate_dropdown_color_to_custom( $atts, $atts, 'background_color', 'custom_background_color', true );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_icon_dropdown_color_to_custom( $atts ) {
		return $this->migrate_dropdown_color_to_custom( $atts, $atts, 'i_color', 'i_custom_color', true );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_icon_background_dropdown_color_to_custom( $atts ) {
		return $this->migrate_dropdown_color_to_custom( $atts, $atts, 'i_background_color', 'i_custom_background_color', true );
	}

	/**
	 * Migrate classic CTA color dropdown to custom_text colorpicker.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_classic_color_to_custom( array $atts ): array {
		$style = $atts['style'] ?? 'classic';
		if ( 'classic' !== $style ) {
			return $atts;
		}

		if ( empty( $atts['color'] ) || 'classic' === $atts['color'] ) {
			return $atts;
		}

		$hash_lib = vc_get_shared( 'dashed-colors-hash' );
		unset( $atts['color'] );

		return $this->migrate_dropdown_color_to_custom( $atts, $atts, 'color', 'custom_text', true, $hash_lib );
	}

	/**
	 * Migrate flat CTA color dropdown to custom colorpicker attributes.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_flat_color_to_custom( array $atts ): array {
		if ( ! isset( $atts['style'] ) || 'flat' !== $atts['style'] ) {
			return $atts;
		}

		if ( empty( $atts['color'] ) ) {
			return $atts;
		}

		$atts = $this->apply_cta_flat_color( $atts, $atts, $atts['color'] );
		unset( $atts['color'] );

		return $atts;
	}

	/**
	 * Migrate 3d CTA color dropdown to custom colorpicker attributes.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_3d_color_to_custom( array $atts ): array {
		if ( ! isset( $atts['style'] ) || '3d' !== $atts['style'] ) {
			return $atts;
		}

		if ( empty( $atts['color'] ) ) {
			return $atts;
		}

		$atts = $this->apply_cta_3d_color( $atts, $atts, $atts['color'] );
		unset( $atts['color'] );

		return $atts;
	}

	/**
	 * Migrate outline CTA color dropdown to custom colorpicker attributes.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_outline_color_to_custom( array $atts ): array {
		if ( ! isset( $atts['style'] ) || 'outline' !== $atts['style'] ) {
			return $atts;
		}

		if ( empty( $atts['color'] ) ) {
			return $atts;
		}

		$atts = $this->apply_cta_outline_color( $atts, $atts, $atts['color'] );
		unset( $atts['color'] );

		return $atts;
	}

	/**
	 * Migrate hover background dropdown color to custom colorpicker.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_hoverbox_background_color_to_custom( $atts ) {
		return $this->migrate_dropdown_color_to_custom( $atts, $atts, 'hover_background_color', 'hover_custom_background', true );
	}

	/**
	 * Migrate gitem element color from block_container to standalone colorpicker attribute.
	 *
	 * Prior to 9.0, the color for vc_gitem_post_title, vc_gitem_post_excerpt,
	 * vc_gitem_post_author, and vc_gitem_post_date was stored inside the block_container
	 * pipe-delimited string. It is now a standalone colorpicker param.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_gitem_color_from_block_container_to_colorpicker( $atts ) {
		return $this->migrate_field_from_container( $atts, 'color', 'block_container' );
	}

	/**
	 * Migrate gitem post title text_align from font_container to standalone button_group attribute.
	 *
	 * Prior to 9.0, the text alignment for vc_gitem_post_title was stored inside the font_container
	 * pipe-delimited string. It is now a standalone button_group param.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_gitem_text_align_from_font_container( $atts ) {
		return $this->migrate_field_from_container( $atts, 'text_align', 'font_container' );
	}

	/**
	 * Extracts a field from a pipe-delimited container attribute into a standalone attribute.
	 *
	 * @param array  $atts
	 * @param string $field_key
	 * @param string $container_key
	 * @return array
	 * @since 9.0
	 */
	public function migrate_field_from_container( $atts, $field_key, $container_key ) {
		if ( ! empty( $atts[ $field_key ] ) || empty( $atts[ $container_key ] ) ) {
			return $atts;
		}

		$parsed = vc_parse_multi_attribute( $atts[ $container_key ] );
		if ( empty( $parsed[ $field_key ] ) ) {
			return $atts;
		}

		$atts[ $field_key ] = $parsed[ $field_key ];
		unset( $parsed[ $field_key ] );
		$parts = [];
		foreach ( $parsed as $key => $value ) {
			$parts[] = $key . ':' . $value;
		}
		$atts[ $container_key ] = implode( '|', $parts );

		return $atts;
	}

	/**
	 * Convert old custom_links + images (comma-separated) to the new JSON images format.
	 *
	 * @since 9.0
	 * @param array $atts
	 * @return array
	 */
	public function convert_gallery_old_custom_link_to_new( $atts ) {
		$is_old_media_library_custom_links = ! empty( $atts['custom_links'] ) &&
			isset( $atts['source'] ) && 'media_library' === $atts['source'] &&
			isset( $atts['onclick'] ) && 'custom_link' === $atts['onclick'];

		if ( $is_old_media_library_custom_links ) {
			$images_data = json_decode( $atts['images'], true );

			if ( is_array( $images_data ) ) {
				return $atts;
			}

			$image_ids = explode( ',', $atts['images'] );
			$custom_links = vc_value_from_safe( $atts['custom_links'] );
			$custom_links = explode( ',', $custom_links );
			$custom_links_target = ! empty( $atts['custom_links_target'] ) ? $atts['custom_links_target'] : '';

			$images_with_links = [];
			foreach ( $image_ids as $i => $image_id ) {
				$image_id = trim( $image_id );
				$link = isset( $custom_links[ $i ] ) ? trim( $custom_links[ $i ] ) : '';
				$images_with_links[ $image_id ] = [
					'url'    => $link,
					'target' => $custom_links_target,
				];
			}

			$atts['images'] = wp_json_encode( $images_with_links );
			unset( $atts['custom_links'] );
		}

		return $atts;
	}

	/**
	 * Migrate count textfield to number with toggle for none numeric values.
	 *
	 * Prior to 9.0 we used textfield for count attr that allow users set 'all' value.
	 * Right now we replaced it with number and toggle 'display_all'
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_posts_slider_count_textfield_to_number( $atts ) {
		if ( isset( $atts['count'] ) ) {
			if ( is_numeric( $atts['count'] ) ) {
				$atts['display_all'] = '';
			} else {
				$atts['display_all'] = 'yes';
				unset( $atts['count'] );
			}
		}

		return $atts;
	}

	/**
	 * Populate the new `items_per_row` att from the legacy `element_width`
	 * (Bootstrap col-span 2/3/4/6/12) when any grid variant is opened in the
	 * editor. Stored `element_width` is left intact so the front-end keeps
	 * rendering identically until the user re-saves the element.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_grid_element_width_to_items_per_row( $atts ) {
		return $this->migrate_grid_element_width_to_items_per_row( $atts, $atts );
	}

	/**
	 * Convert legacy `filter_color` dropdown slug to its colorpicker hex value.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_grid_filter_color_to_custom( $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $atts, 'filter_color' );
	}

	/**
	 * Convert legacy `arrows_color` dropdown slug to its colorpicker hex value.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_grid_arrows_color_to_custom( $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $atts, 'arrows_color' );
	}

	/**
	 * Convert legacy `paging_color` dropdown slug to its colorpicker hex value.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_grid_paging_color_to_custom( $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $atts, 'paging_color' );
	}

	/**
	 * Migrates dropdown button color to inline custom colorpicker attributes.
	 *
	 * Handles modern, classic, and flat styles. Modern sets all six custom_* params;
	 * classic and flat omit border params.
	 *
	 * @param array  $atts
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_dropdown_color_to_custom( array $atts, string $prefix = '' ): array {
		if ( ! isset( $atts[ $prefix . 'style' ] ) || ! in_array( $atts[ $prefix . 'style' ], [ 'modern', 'classic', 'flat' ], true ) ) {
			return $atts;
		}

		if ( ! isset( $atts[ $prefix . 'color' ] ) ) {
			return $atts;
		}

		return $this->apply_btn_solid_color( $atts, $atts[ $prefix . 'color' ], $atts[ $prefix . 'style' ], $prefix );
	}

	/**
	 * Migrates 3d style dropdown button color to inline custom colorpicker attributes.
	 *
	 * Maps the dropdown color slug to background, text, and box-shadow (custom_border) values.
	 *
	 * @param array $atts
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_3d_dropdown_color_to_custom( array $atts, string $prefix = '' ): array {
		if ( ! isset( $atts[ $prefix . 'style' ] ) || '3d' !== $atts[ $prefix . 'style' ] ) {
			return $atts;
		}

		if ( empty( $atts[ $prefix . 'color' ] ) ) {
			return $atts;
		}

		return $this->apply_btn_3d_color( $atts, $atts[ $prefix . 'color' ], $prefix );
	}

	/**
	 * Migrates outline style dropdown button color to inline custom colorpicker attributes.
	 *
	 * Outline omits custom_background (always transparent) and uses the accent color
	 * as border/hover-background/hover-border.
	 *
	 * @param array $atts
	 * @param string $prefix used for integrated buttons.
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_outline_dropdown_color_to_custom( array $atts, string $prefix = '' ): array {
		if ( ! isset( $atts[ $prefix . 'style' ] ) || 'outline' !== $atts[ $prefix . 'style' ] ) {
			return $atts;
		}

		if ( ! isset( $atts[ $prefix . 'color' ] ) ) {
			return $atts;
		}

		return $this->apply_btn_outline_color( $atts, $atts[ $prefix . 'color' ], $prefix );
	}

	/**
	 * Backfills default outline colors for an outline button saved without explicit color selection.
	 *
	 * @param array  $atts
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_default_outline_dropdown_color_to_custom( array $atts, string $prefix = '' ): array {
		if ( ! isset( $atts[ $prefix . 'style' ] ) || 'outline' !== $atts[ $prefix . 'style' ] ) {
			return $atts;
		}

		if ( ! empty( $atts[ $prefix . 'color' ] ) || ! empty( $atts[ $prefix . 'custom_text' ] ) ) {
			return $atts;
		}

		return $this->apply_btn_outline_color( $atts, 'grey', $prefix );
	}

	/**
	 * Backfills default 3d colors for a 3d button saved without explicit color selection.
	 *
	 * @param array  $atts
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_default_3d_dropdown_color_to_custom( array $atts, string $prefix = '' ): array {
		if ( ! isset( $atts[ $prefix . 'style' ] ) || '3d' !== $atts[ $prefix . 'style' ] ) {
			return $atts;
		}

		if ( ! empty( $atts[ $prefix . 'color' ] ) || ! empty( $atts[ $prefix . 'custom_background' ] ) ) {
			return $atts;
		}

		return $this->apply_btn_3d_color( $atts, 'grey', $prefix );
	}

	/**
	 * B.C for a gradient buttons with dropdown colors we convert them to custom-gradien style.
	 *
	 * @param array  $atts
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_gradient_style_to_gradient_custom( array $atts, string $prefix = '' ): array {
		if ( ! isset( $atts[ $prefix . 'style' ] ) || 'gradient' !== $atts[ $prefix . 'style' ] ) {
			return $atts;
		}

		$atts[ $prefix . 'style' ] = 'gradient-custom';

		if ( empty( $atts[ $prefix . 'gradient_color_1' ] ) ) {
			$atts[ $prefix . 'gradient_custom_color_1' ] = vc_convert_vc_color( 'turquoise' );
		} else {
			$atts[ $prefix . 'gradient_custom_color_1' ] = vc_convert_vc_color( $atts[ $prefix . 'gradient_color_1' ] );
			unset( $atts[ $prefix . 'gradient_color_1' ] );
		}

		if ( empty( $atts[ $prefix . 'gradient_color_2' ] ) ) {
			$atts[ $prefix . 'gradient_custom_color_2' ] = vc_convert_vc_color( 'blue' );
		} else {
			$atts[ $prefix . 'gradient_custom_color_2' ] = vc_convert_vc_color( $atts[ $prefix . 'gradient_color_2' ] );
			unset( $atts[ $prefix . 'gradient_color_2' ] );
		}

		$atts[ $prefix . 'gradient_text_color' ] = '#fff';

		return $atts;
	}

	/**
	 * Migrate top-level bgcolor dropdown to custombgcolor colorpicker.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_progress_bar_bgcolor_to_custom( $atts ) {
		if ( empty( $atts['bgcolor'] ) ) {
			return $atts;
		}

		$atts = $this->apply_progress_bar_color( $atts, $atts['bgcolor'], 'custombgcolor', 'customtxtcolor' );

		unset( $atts['bgcolor'] );

		return $atts;
	}

	/**
	 * Migrate per-bar color dropdown inside values param_group to customcolor colorpicker.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_progress_bar_values_color_to_custom( $atts ) {
		if ( ! empty( $atts['values'] ) ) {
			$atts['values'] = $this->migrate_progress_bar_values_color( $atts['values'] );
		}

		return $atts;
	}

	/**
	 * Migrate legacy options checkbox to separate striped/animated toggle attributes.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_progress_bar_options_checkbox_to_toggles( $atts ) {
		return $this->migrate_progress_bar_options_to_toggles( $atts, $atts );
	}

	/**
	 * Migrate dropdown stroke color to custom colorpicker.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_round_chart_stroke_color_to_custom( $atts ) {
		$hash_lib = vc_get_shared( 'dashed-colors-hash' );
		return $this->migrate_dropdown_color_to_custom(
			$atts,
			$atts,
			'stroke_color',
			'custom_stroke_color',
			true,
			$hash_lib
		);
	}

	/**
	 * Migrate dropdown legend color to custom colorpicker.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_round_chart_legend_color_to_custom( $atts ) {
		$hash_lib = vc_get_shared( 'dashed-colors-hash' );
		return $this->migrate_dropdown_color_to_custom(
			$atts,
			$atts,
			'legend_color',
			'custom_legend_color',
			true,
			$hash_lib
		);
	}

	/**
	 * Migrate per-item color dropdown inside values param group to custom_color colorpicker.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_round_chart_values_color_to_custom( $atts ) {
		return $this->migrate_chart_values_color( $atts );
	}

	/**
	 * Migrate per-item color dropdown inside values param group to custom_color colorpicker.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_line_chart_values_color_to_custom( $atts ) {
		return $this->migrate_chart_values_color( $atts );
	}

	/**
	 * Migrate dropdown color to custom colorpicker.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_pie_chart_color_to_custom( $atts ) {
		$hash_lib = vc_get_shared( 'dashed-colors-hash' );
		return $this->migrate_dropdown_color_to_custom(
			$atts,
			$atts,
			'color',
			'custom_color',
			true,
			$hash_lib
		);
	}

	/**
	 * Sanitize the images carousel speed attribute to a numeric value.
	 *
	 * Prior to 9.0, the speed value could be stored with a unit suffix
	 * like "5000ms". Strip any non-numeric characters so only the integer
	 * value remains (e.g. "5000ms" becomes 5000).
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_images_carousel_speed_to_numeric( $atts ) {
		if ( isset( $atts['speed'] ) && '' !== $atts['speed'] ) {
			$atts['speed'] = (string) (int) preg_replace( '/[^0-9]/', '', (string) $atts['speed'] );
		}
		return $atts;
	}

	/**
	 * Convert elements with integrated button to new button colors param.
	 *
	 * @param array $atts
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_integrated_btn( $atts, $prefix = 'btn_' ) {
		$atts = $this->convert_btn_outline_dropdown_color_to_custom( $atts, $prefix );
		$atts = $this->convert_btn_3d_dropdown_color_to_custom( $atts, $prefix );
		$atts = $this->convert_btn_dropdown_color_to_custom( $atts, $prefix );
		$atts = $this->convert_btn_default_outline_dropdown_color_to_custom( $atts, $prefix );
		$atts = $this->convert_btn_default_3d_dropdown_color_to_custom( $atts, $prefix );
		$atts = $this->convert_btn_gradient_style_to_gradient_custom( $atts, $prefix );

		return $atts;
	}

	/**
	 * Convert vc_hoverbox integrated button to new params.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_vc_hoverbox_integrated_btn( $atts ) {
		return $this->convert_integrated_btn( $atts, 'hover_btn_' );
	}

	/**
	 * Convert legacy dropdown pagination_color of vc_tta_tabs to a colorpicker value.
	 *
	 * Prior to 9.0, vc_tta_tabs `pagination_color` was a dropdown that stored a
	 * predefined color slug (e.g. "grey", "juicy_pink"). It is now a colorpicker
	 * storing a hex/rgb value. Resolve legacy slugs through the colors-hash map so
	 * the value renders correctly in the edit modal.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_tabs_pagination_color_to_custom( $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $atts, 'pagination_color' );
	}

	/**
	 * Convert legacy dropdown color of vc_tta_tabs to a colorpicker value.
	 *
	 * Prior to 9.0, vc_tta_tabs `color` was a dropdown that stored a predefined
	 * color slug (e.g. "grey", "juicy_pink"). It is now a colorpicker storing a
	 * hex/rgb value. Resolve legacy slugs through the colors-hash map so the
	 * value renders correctly in the edit modal.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_tabs_color_to_custom( $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $atts, 'color' );
	}

	/**
	 * Back-compat for the new `active_color` param of vc_tta_tabs (edit form).
	 *
	 * See {@see Wpb_Template_Attributes_Migration::convert_tta_tabs_active_color_to_color()}
	 * for the full rationale. This variant runs on the raw saved atts before they
	 * are merged with the registered defaults, so the edit modal opens with the
	 * legacy single-color look preserved (active_color mirrors color) instead of
	 * the new `#F8F8F8` std silently replacing the user's saved value.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_tabs_active_color_to_color( $atts ) {
		if ( ! empty( $atts['active_color'] ) ) {
			return $atts;
		}

		$style = isset( $atts['style'] ) ? (string) $atts['style'] : '';
		if ( 'flat' !== $style ) {
			return $atts;
		}

		if ( ! empty( $atts['color'] ) ) {
			$atts['active_color'] = $atts['color'];
		}

		return $atts;
	}

	/**
	 * Pre-fill the title color pickers with the value the render already applies.
	 *
	 * The title colors fall back to the automatic contrast of their background, so an
	 * unset picker would look empty while the heading still renders white/dark. Populate
	 * the fields so the edit form reflects what is actually shown. Runs after
	 * {@see self::convert_tta_tabs_active_color_to_color()} so the active background is known.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_title_colors_to_contrast( $atts ) {
		$color = isset( $atts['color'] ) ? (string) $atts['color'] : '';
		if ( '' === $color ) {
			return $atts;
		}

		$style = isset( $atts['style'] ) ? (string) $atts['style'] : '';

		$active_bg = ! empty( $atts['active_color'] ) ? (string) $atts['active_color'] : '#F8F8F8';
		if ( 'outline' === $style ) {
			$inactive_title = $color;
			$active_title = $color;
		} else {
			$inactive_title = $this->get_tta_contrast_color( $color );
			$active_title = $this->get_tta_contrast_color( $active_bg );
		}

		if ( empty( $atts['inactive_title_color'] ) ) {
			$atts['inactive_title_color'] = $inactive_title;
		}
		if ( empty( $atts['active_title_color'] ) ) {
			$atts['active_title_color'] = $active_title;
		}

		return $atts;
	}

	/**
	 * Map legacy outline `color` to the dedicated outline color field (edit form).
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_accordion_outline_color( $atts ) {
		if ( 'outline' !== (string) ( $atts['style'] ?? '' ) ) {
			return $atts;
		}

		$color = (string) ( $atts['color'] ?? '' );
		if ( '' !== $color && empty( $atts['outline_color'] ) ) {
			$atts['outline_color'] = $color;
		}

		return $atts;
	}

	/**
	 * Mirror of WPBakeryShortCode_Vc_Tta_Accordion::getContrastColor().
	 *
	 * @param string $value
	 * @return string
	 * @since 9.0
	 */
	protected function get_tta_contrast_color( $value ) {
		$rgb = Vc_Color_Helper::hexToRgb( $value );
		if ( null === $rgb ) {
			return '#fff';
		}

		$luminance = ( $rgb['R'] * 0.299 + $rgb['G'] * 0.587 + $rgb['B'] * 0.114 ) / 255;

		return $luminance > 0.7 ? '#666' : '#fff';
	}

	/**
	 * Migrate legacy options checkbox to separate type dropdown and count toggle attributes.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_wp_archives_options_to_type_and_count( $atts ) {
		return $this->migrate_wp_archives_options_to_type_and_count( $atts, $atts );
	}

	/**
	 * Migrate legacy options checkbox to separate item_content/item_author/item_date toggle attributes.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_wp_rss_options_checkbox_to_toggles( $atts ) {
		return $this->migrate_wp_rss_options_to_toggles( $atts, $atts );
	}

	/**
	 * Backward compatibility for the renamed `content_placement` param of vc_section.
	 *
	 * See {@see Wpb_Template_Attributes_Migration::convert_section_content_placement_to_vertical_content_position()}
	 * for the full rationale. This variant runs on the raw saved atts so the edit
	 * modal opens with the legacy content position pre-selected under the new
	 * `vertical_content_position` param.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_section_content_placement_to_vertical_content_position( $atts ) {
		return $this->migrate_renamed_attribute( $atts, $atts, 'content_placement', 'vertical_content_position', true );
	}


	/**
	 * Migrate the `autoplay` default value from 0 to 7 for vc_tta_pageable elements.
	 *
	 * @param array $out      Accumulated output attributes passed through the filter chain.
	 * @param array $settings Element shortcode settings from the mapper.
	 * @param array $atts     Raw saved shortcode attributes for the element being edited.
	 * @return array
	 * @since 9.0
	 */
	public function update_tta_pageable_autoplay_default( $out, $settings, $atts ) {
		// add new element case.
		if ( [] === $out && [] === $atts ) {
			return $out;
		}

		if ( ! isset( $atts['autoplay'] ) ) {
			$out['autoplay'] = '0';
		}

		return $out;
	}
}
