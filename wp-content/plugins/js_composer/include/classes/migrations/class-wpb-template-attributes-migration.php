<?php
/**
 * Templates attributes migration class.
 * We use it when changing attributes in our templates
 * and want to provide backward compatibility.
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'MIGRATIONS_DIR', 'abstract-class-wpb-attributes-migration.php' );

/**
 * Class Wpb_Template_Attributes_Migration
 *
 * @since 9.0
 */
class Wpb_Template_Attributes_Migration extends Wpb_Attributes_Migration_Abstract {
	/**
	 * Init hooks.
	 *
	 * @since 9.0
	 */
	public function init() {
		add_filter( 'shortcode_atts_vc_separator', [ $this, 'convert_separator_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_text_separator', [ $this, 'convert_separator_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_zigzag', [ $this, 'convert_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_icon', [ $this, 'convert_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_icon', [ $this, 'convert_background_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_text_separator', [ $this, 'convert_icon_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_text_separator', [ $this, 'convert_icon_background_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_icon_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_icon_background_dropdown_color_to_custom' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_gitem_post_categories', [ $this, 'convert_category_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_gitem_image', [ $this, 'convert_border_dropdown_color_to_custom' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_posts_slider', [ $this, 'convert_posts_slider_count_textfield_to_number' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_btn', [ $this, 'convert_btn_default_outline_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_btn', [ $this, 'convert_btn_default_3d_dropdown_color_to_custom' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_btn', [ $this, 'convert_btn_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_btn', [ $this, 'convert_btn_outline_dropdown_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_btn', [ $this, 'convert_btn_3d_dropdown_color_to_custom' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_btn', [ $this, 'convert_btn_gradient_style_to_gradient_custom' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_progress_bar', [ $this, 'convert_progress_bar_bgcolor_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_progress_bar', [ $this, 'convert_progress_bar_values_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_progress_bar', [ $this, 'convert_progress_bar_options_checkbox_to_toggles' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_images_carousel', [ $this, 'convert_images_carousel_speed_to_numeric' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_round_chart', [ $this, 'convert_round_chart_stroke_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_round_chart', [ $this, 'convert_round_chart_legend_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_round_chart', [ $this, 'convert_round_chart_values_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_round_chart', [ $this, 'migrate_round_chart_style_custom_to_flat' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_line_chart', [ $this, 'convert_line_chart_values_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_line_chart', [ $this, 'migrate_line_chart_style_custom_to_flat' ] );

		add_filter( 'shortcode_atts_vc_pie', [ $this, 'convert_pie_chart_color_to_custom' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_hoverbox', [ $this, 'convert_hoverbox_background_color_to_custom' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_pricing_table', [ $this, 'convert_integrated_btn' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_hoverbox', [ $this, 'convert_vc_hoverbox_integrated_btn' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_wp_archives', [ $this, 'convert_wp_archives_options_to_type_and_count' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_wp_rss', [ $this, 'convert_wp_rss_options_checkbox_to_toggles' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_tta_tabs', [ $this, 'convert_tta_tabs_pagination_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_tta_tabs', [ $this, 'convert_tta_tabs_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_tta_tabs', [ $this, 'convert_tta_tabs_active_color_to_color' ], 11, 3 );

		add_filter( 'shortcode_atts_vc_tta_tabs', [ $this, 'convert_tta_no_fill_to_fill_content_area' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_tta_tour', [ $this, 'convert_tta_tabs_pagination_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_tta_tour', [ $this, 'convert_tta_tabs_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_tta_tour', [ $this, 'convert_tta_tabs_active_color_to_color' ], 11, 3 );

		add_filter( 'shortcode_atts_vc_tta_tour', [ $this, 'convert_tta_no_fill_to_fill_content_area' ], 10, 3 );
		// vc_tta_accordion shares the same color/active_color/fill schema as vc_tta_tabs.
		add_filter( 'shortcode_atts_vc_tta_accordion', [ $this, 'convert_tta_tabs_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_tta_accordion', [ $this, 'convert_tta_tabs_active_color_to_color' ], 11, 3 );
		add_filter( 'shortcode_atts_vc_tta_accordion', [ $this, 'convert_tta_accordion_outline_color' ], 12, 3 );

		add_filter( 'shortcode_atts_vc_tta_accordion', [ $this, 'convert_tta_no_fill_to_fill_content_area' ], 10, 3 );

		// vc_tta_pageable shares the same legacy dropdown pagination_color schema.
		add_filter( 'shortcode_atts_vc_tta_pageable', [ $this, 'convert_tta_tabs_pagination_color_to_custom' ], 10, 3 );

		foreach ( [ 'vc_basic_grid', 'vc_masonry_grid', 'vc_media_grid', 'vc_masonry_media_grid' ] as $grid_base ) {
			add_filter( 'shortcode_atts_' . $grid_base, [ $this, 'convert_grid_element_width_to_items_per_row' ], 10, 3 );
			add_filter( 'shortcode_atts_' . $grid_base, [ $this, 'normalize_grid_gap_to_px' ], 10, 3 );
			add_filter( 'shortcode_atts_' . $grid_base, [ $this, 'convert_grid_filter_color_to_custom' ], 10, 3 );
			add_filter( 'shortcode_atts_' . $grid_base, [ $this, 'convert_grid_arrows_color_to_custom' ], 10, 3 );
			add_filter( 'shortcode_atts_' . $grid_base, [ $this, 'convert_grid_paging_color_to_custom' ], 10, 3 );
			add_filter( 'shortcode_atts_' . $grid_base, [ $this, 'convert_integrated_btn' ], 10, 3 );
		}

		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_cta_classic_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_cta_flat_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_cta_3d_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_cta_outline_color_to_custom' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_cta_el_width_dropdown_to_range' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_cta_add_button_dropdown_to_toggle' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_cta_add_icon_dropdown_to_toggle' ], 10, 3 );
		add_filter( 'shortcode_atts_vc_cta', [ $this, 'convert_integrated_btn' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_section', [ $this, 'convert_section_content_placement_to_vertical_content_position' ], 10, 3 );

		add_filter( 'shortcode_atts_vc_tta_pageable', [ $this, 'update_tta_pageable_autoplay_default' ], 10, 3 );

		add_filter( 'the_editor_content', [ $this, 'backfill_tta_pageable_autoplay_in_editor' ] );

		add_action( 'vc_after_init', [ $this, 'register_legacy_params' ], 1 );
	}

	/**
	 * Backward compatibility for the renamed `no_fill_content_area` param.
	 *
	 * The legacy checkbox `no_fill_content_area` has been replaced by a new
	 * toggle param `fill_content_area` with inverted (non-misleading) semantics.
	 * For elements saved before the rename we derive the new value from the old
	 * one so the rendered output stays identical.
	 *
	 * Mapping:
	 *  - no_fill_content_area = 'true'  -> fill_content_area = ''     (do not fill)
	 *  - no_fill_content_area = '' / 'false' / absent (legacy default was "fill")
	 *                                   -> fill_content_area = 'true' (fill)
	 *
	 * Only applied when the new param is NOT already present in the raw atts,
	 * so brand-new elements (which always save the new param) are left alone.
	 *
	 * @param array $out   Merged shortcode attributes.
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_no_fill_to_fill_content_area( $out, $pairs, $atts ) {
		$atts = (array) $atts;

		if ( array_key_exists( 'fill_content_area', $atts ) ) {
			return $out;
		}

		if ( array_key_exists( 'no_fill_content_area', $atts ) ) {
			$out['fill_content_area'] = ( 'true' === $atts['no_fill_content_area'] ) ? '' : 'true';
		} elseif ( array_key_exists( 'no_fill', $atts ) ) {
			// vc_tta_accordion used the `no_fill` param before the rename.
			$out['fill_content_area'] = ( 'true' === $atts['no_fill'] ) ? '' : 'true';
		} else {
			// Legacy element saved before either param existed: legacy default was "fill".
			$out['fill_content_area'] = 'true';
		}

		return $out;
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $out    Merged shortcode attributes (registered keys only).
	 * @param array $pairs  Registered attribute defaults.
	 * @param array $atts   Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_border_dropdown_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_dropdown_color_to_custom( $out, $atts, 'border_color', 'custom_border_color' );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $out    Merged shortcode attributes (registered keys only).
	 * @param array $pairs  Registered attribute defaults.
	 * @param array $atts   Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_category_dropdown_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_dropdown_color_to_custom( $out, $atts, 'category_color', 'custom_category_color' );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $out    Merged shortcode attributes (registered keys only).
	 * @param array $pairs  Registered attribute defaults.
	 * @param array $atts   Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_separator_dropdown_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_dropdown_color_to_custom( $out, $atts, 'color', 'accent_color' );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $out    Merged shortcode attributes (registered keys only).
	 * @param array $pairs  Registered attribute defaults.
	 * @param array $atts   Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_dropdown_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_dropdown_color_to_custom( $out, $atts, 'color', 'custom_color' );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $out    Merged shortcode attributes (registered keys only).
	 * @param array $pairs  Registered attribute defaults.
	 * @param array $atts   Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_background_dropdown_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_dropdown_color_to_custom( $out, $atts, 'background_color', 'custom_background_color' );
	}

	/**
	 * Migrate dropdown color.
	 *
	 * @param array $out    Merged shortcode attributes (registered keys only).
	 * @param array $pairs  Registered attribute defaults.
	 * @param array $atts   Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_icon_dropdown_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_dropdown_color_to_custom( $out, $atts, 'i_color', 'i_custom_color' );
	}


	/**
	 * Migrate dropdown color.
	 *
	 * @param array $out    Merged shortcode attributes (registered keys only).
	 * @param array $pairs  Registered attribute defaults.
	 * @param array $atts   Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_icon_background_dropdown_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_dropdown_color_to_custom( $out, $atts, 'i_background_color', 'i_custom_background_color' );
	}

	/**
	 * Migrate classic CTA color dropdown to custom_text colorpicker.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_classic_color_to_custom( array $out, $pairs, array $atts ): array {
		$style = isset( $out['style'] ) ? $out['style'] : 'classic';
		if ( 'classic' !== $style ) {
			return $out;
		}

		if ( empty( $atts['color'] ) || 'classic' === $atts['color'] ) {
			return $out;
		}

		$hash_lib = vc_get_shared( 'dashed-colors-hash' );

		return $this->migrate_dropdown_color_to_custom( $out, $atts, 'color', 'custom_text', false, $hash_lib );
	}

	/**
	 * Migrate flat CTA color dropdown to custom colorpicker attributes.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_flat_color_to_custom( array $out, $pairs, array $atts ): array {
		if ( ! isset( $out['style'] ) || 'flat' !== $out['style'] ) {
			return $out;
		}

		if ( empty( $atts['color'] ) ) {
			return $out;
		}

		return $this->apply_cta_flat_color( $out, $atts, $atts['color'], true );
	}

	/**
	 * Migrate 3d CTA color dropdown to custom colorpicker attributes.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_3d_color_to_custom( array $out, $pairs, array $atts ): array {
		if ( ! isset( $out['style'] ) || '3d' !== $out['style'] ) {
			return $out;
		}

		if ( empty( $atts['color'] ) ) {
			return $out;
		}

		return $this->apply_cta_3d_color( $out, $atts, $atts['color'], true );
	}

	/**
	 * Migrate outline CTA color dropdown to custom colorpicker attributes.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_outline_color_to_custom( array $out, $pairs, array $atts ): array {
		if ( ! isset( $out['style'] ) || 'outline' !== $out['style'] ) {
			return $out;
		}

		if ( empty( $atts['color'] ) ) {
			return $out;
		}

		return $this->apply_cta_outline_color( $out, $atts, $atts['color'] );
	}

	/**
	 * Migrate hover background dropdown color to custom colorpicker.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_hoverbox_background_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_dropdown_color_to_custom( $out, $atts, 'hover_background_color', 'hover_custom_background' );
	}

	/**
	 * Migrates modern/classic/flat style button color dropdown to inline custom colorpicker attributes.
	 *
	 * Handles modern, classic, and flat styles. Modern sets all six custom_* params;
	 * classic and flat omit border params.
	 *
	 * @param array  $out     Merged shortcode attributes.
	 * @param array  $pairs Registered attribute defaults.
	 * @param array  $atts Original raw shortcode attributes.
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_dropdown_color_to_custom( array $out, $pairs, $atts, string $prefix = '' ): array {
		if ( ! isset( $out[ $prefix . 'style' ] ) || ! in_array( $out[ $prefix . 'style' ], [ 'modern', 'classic', 'flat' ], true ) ) {
			return $out;
		}

		if ( empty( $atts[ $prefix . 'color' ] ) ) {
			return $out;
		}

		return $this->apply_btn_solid_color( $out, $atts[ $prefix . 'color' ], $out[ $prefix . 'style' ], $prefix );
	}

	/**
	 * Migrates 3d style button color dropdown to inline custom colorpicker attributes.
	 *
	 * Maps the dropdown color slug to background, text, and box-shadow (custom_border) values.
	 *
	 * @param array  $out     Merged shortcode attributes.
	 * @param array  $pairs Registered attribute defaults.
	 * @param array  $atts Original raw shortcode attributes.
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_3d_dropdown_color_to_custom( array $out, $pairs, $atts, string $prefix = '' ): array {
		if ( ! isset( $atts[ $prefix . 'style' ] ) || '3d' !== $atts[ $prefix . 'style' ] ) {
			return $out;
		}

		if ( empty( $atts[ $prefix . 'color' ] ) ) {
			return $out;
		}

		return $this->apply_btn_3d_color( $out, $atts[ $prefix . 'color' ], $prefix );
	}

	/**
	 * Migrates outline style button color dropdown to inline custom colorpicker attributes.
	 *
	 * Outline omits custom_background (always transparent) and uses the accent color
	 * as border/hover-background/hover-border.
	 *
	 * @param array  $out     Merged shortcode attributes.
	 * @param array  $pairs Registered attribute defaults.
	 * @param array  $atts Original raw shortcode attributes.
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_outline_dropdown_color_to_custom( array $out, $pairs, $atts, string $prefix = '' ): array {
		if ( ! isset( $atts[ $prefix . 'style' ] ) || 'outline' !== $atts[ $prefix . 'style' ] ) {
			return $out;
		}

		if ( empty( $atts[ $prefix . 'color' ] ) ) {
			return $out;
		}

		return $this->apply_btn_outline_color( $out, $atts[ $prefix . 'color' ], $prefix );
	}

	/**
	 * B.C for a gradient buttons with dropdown colors we convert them to custom-gradien style.
	 *
	 * @param array $out   Merged shortcode attributes.
	 * @param array  $pairs Registered attribute defaults.
	 * @param array  $atts  Original raw shortcode attributes.
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_gradient_style_to_gradient_custom( array $out, $pairs, $atts, string $prefix = '' ): array {
		if ( ! isset( $out[ $prefix . 'style' ] ) || 'gradient' !== $out[ $prefix . 'style' ] ) {
			return $out;
		}

		$out[ $prefix . 'style' ] = 'gradient-custom';

		if ( ! isset( $atts[ $prefix . 'gradient_color_1' ] ) ) {
			$out[ $prefix . 'gradient_custom_color_1' ] = vc_convert_vc_color( 'turquoise' );
		} else {
			$out[ $prefix . 'gradient_custom_color_1' ] = vc_convert_vc_color( $atts[ $prefix . 'gradient_color_1' ] );
			unset( $out[ $prefix . 'gradient_color_1' ] );
		}

		if ( ! isset( $atts[ $prefix . 'gradient_color_2' ] ) ) {
			$out[ $prefix . 'gradient_custom_color_2' ] = vc_convert_vc_color( 'blue' );
		} else {
			$out[ $prefix . 'gradient_custom_color_2' ] = vc_convert_vc_color( $atts[ $prefix . 'gradient_color_2' ] );
			unset( $out[ $prefix . 'gradient_color_2' ] );
		}

		$out[ $prefix . 'gradient_text_color' ] = '#fff';

		return $out;
	}

	/**
	 * Backfills default outline colors for an outline button saved without explicit color selection.
	 *
	 * @param array  $out   Merged shortcode attributes.
	 * @param array  $pairs Registered attribute defaults.
	 * @param array  $atts  Original raw shortcode attributes.
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_default_outline_dropdown_color_to_custom( array $out, $pairs, $atts, string $prefix = '' ): array {
		if ( ! isset( $out[ $prefix . 'style' ] ) || 'outline' !== $out[ $prefix . 'style' ] ) {
			return $out;
		}

		if ( $this->is_custom_button_atts( $atts, $prefix ) ) {
			return $out;
		}

		if ( ! empty( $atts[ $prefix . 'color' ] ) ) {
			return $out;
		}

		return $this->apply_btn_outline_color( $out, 'grey', $prefix );
	}

	/**
	 * Backfills default 3d colors for a 3d button saved without explicit color selection.
	 *
	 * @param array  $out   Merged shortcode attributes.
	 * @param array  $pairs Registered attribute defaults.
	 * @param array  $atts  Original raw shortcode attributes.
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_btn_default_3d_dropdown_color_to_custom( array $out, $pairs, $atts, string $prefix = '' ): array {
		if ( ! isset( $out[ $prefix . 'style' ] ) || '3d' !== $out[ $prefix . 'style' ] ) {
			return $out;
		}

		if ( $this->is_custom_button_atts( $atts, $prefix ) ) {
			return $out;
		}

		if ( ! empty( $atts[ $prefix . 'color' ] ) ) {
			return $out;
		}

		return $this->apply_btn_3d_color( $out, 'grey', $prefix );
	}

	/**
	 * Migrate top-level bgcolor dropdown to custombgcolor colorpicker.
	 *
	 * @param array $out   Merged shortcode attributes.
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_progress_bar_bgcolor_to_custom( $out, $pairs, $atts ) {
		if ( empty( $atts['bgcolor'] ) ) {
			return $out;
		}

		$out = $this->apply_progress_bar_color( $out, $atts['bgcolor'], 'custombgcolor', 'customtxtcolor' );

		return $out;
	}

	/**
	 * Migrate per-bar color dropdown in values param group to customcolor/customtxtcolor.
	 *
	 * @param array $out   Merged shortcode attributes.
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_progress_bar_values_color_to_custom( $out, $pairs, $atts ) {
		if ( ! empty( $atts['values'] ) ) {
			$out['values'] = $this->migrate_progress_bar_values_color( $atts['values'] );
		}

		return $out;
	}

	/**
	 * Check if button has custom button attributes.
	 *
	 * @since 9.0
	 * @param array  $atts
	 * @param string $prefix
	 * @return bool
	 */
	public function is_custom_button_atts( $atts, string $prefix = '' ): bool {
		$custom_button_atts = [
			'custom_background',
			'custom_text',
			'custom_border',
			'custom_hover_background',
			'custom_hover_text',
			'custom_hover_border',
		];

		foreach ( $custom_button_atts as $custom_button_atts_value ) {
			if ( array_key_exists( $prefix . $custom_button_atts_value, $atts ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Migrate legacy options checkbox to separate striped/animated toggle attributes.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_progress_bar_options_checkbox_to_toggles( $out, $pairs, $atts ) {
		return $this->migrate_progress_bar_options_to_toggles( $out, $atts );
	}

	/**
	 * Migrate dropdown stroke color to custom colorpicker.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_round_chart_stroke_color_to_custom( $out, $pairs, $atts ) {
		$hash_lib = vc_get_shared( 'dashed-colors-hash' );
		return $this->migrate_dropdown_color_to_custom(
			$out,
			$atts,
			'stroke_color',
			'custom_stroke_color',
			false,
			$hash_lib
		);
	}

	/**
	 * Migrate dropdown legend color to custom colorpicker.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_round_chart_legend_color_to_custom( $out, $pairs, $atts ) {
		$hash_lib = vc_get_shared( 'dashed-colors-hash' );
		return $this->migrate_dropdown_color_to_custom(
			$out,
			$atts,
			'legend_color',
			'custom_legend_color',
			false,
			$hash_lib
		);
	}

	/**
	 * Migrate per-item color dropdown inside values param group to custom_color colorpicker.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_round_chart_values_color_to_custom( $out, $pairs, $atts ) {
		$migrated = $this->migrate_chart_values_color( $atts );
		if ( isset( $migrated['values'] ) ) {
			$out['values'] = $migrated['values'];
		}
		return $out;
	}

	/**
	 * Migrate per-item color dropdown inside values param group to custom_color colorpicker.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_line_chart_values_color_to_custom( $out, $pairs, $atts ) {
		$migrated = $this->migrate_chart_values_color( $atts );
		if ( isset( $migrated['values'] ) ) {
			$out['values'] = $migrated['values'];
		}
		return $out;
	}

	/**
	 * Migrate dropdown color to custom colorpicker.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_pie_chart_color_to_custom( $out, $pairs, $atts ) {
		$hash_lib = vc_get_shared( 'dashed-colors-hash' );
		return $this->migrate_dropdown_color_to_custom(
			$out,
			$atts,
			'color',
			'custom_color',
			false,
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
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_images_carousel_speed_to_numeric( $out, $pairs, $atts ) {
		if ( isset( $out['speed'] ) && '' !== $out['speed'] ) {
			$out['speed'] = (string) (int) preg_replace( '/[^0-9]/', '', (string) $out['speed'] );
		}
		return $out;
	}

	/**
	 * Convert legacy dropdown pagination_color of vc_tta_tabs to a colorpicker value.
	 *
	 * Prior to 9.0, vc_tta_tabs `pagination_color` was a dropdown that stored a
	 * predefined color slug (e.g. "grey", "juicy_pink"). It is now a colorpicker
	 * storing a hex/rgb value. Resolve legacy slugs through the colors-hash map
	 * so the saved value renders correctly with the new CSS variable approach.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_tabs_pagination_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $out, 'pagination_color' );
	}

	/**
	 * Convert legacy dropdown color of vc_tta_tabs to a colorpicker value.
	 *
	 * Prior to 9.0, vc_tta_tabs `color` was a dropdown that stored a predefined
	 * color slug (e.g. "grey", "juicy_pink"). It is now a colorpicker storing a
	 * hex/rgb value. Resolve legacy slugs through the colors-hash map so the
	 * saved value renders correctly with the new CSS variable approach.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_tabs_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $out, 'color' );
	}

	/**
	 * Convert legacy `filter_color` dropdown slug to its colorpicker hex value
	 * at render time so saved posts keep their color before re-save.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_grid_filter_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $out, 'filter_color' );
	}

	/**
	 * Convert legacy `arrows_color` dropdown slug to its colorpicker hex value at
	 * render time so saved grids keep their arrow color before re-save.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_grid_arrows_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $out, 'arrows_color' );
	}

	/**
	 * Convert legacy `paging_color` dropdown slug to its colorpicker hex value at
	 * render time so saved grids keep their pagination color before re-save.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_grid_paging_color_to_custom( $out, $pairs, $atts ) {
		return $this->migrate_inline_dropdown_color_to_custom( $out, 'paging_color' );
	}

	/**
	 * Populate `items_per_row` from the legacy `element_width` (Bootstrap col-span)
	 * at render time so saved posts keep their grid layout before re-save.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_grid_element_width_to_items_per_row( $out, $pairs, $atts ) {
		return $this->migrate_grid_element_width_to_items_per_row( $out, $atts );
	}

	/**
	 * Back-compat for the new `active_color` param of vc_tta_tabs.
	 *
	 * Prior to 9.0 the element exposed a single `color` attribute that controlled
	 * the tabs coloring. The historical behavior differed per style:
	 *
	 *  - Flat: painted both inactive AND active tabs with the same picked color
	 *    (active was just slightly lighter via a `darken()` tweak).
	 *  - Classic / Modern / Outline: painted only the inactive tab with the picked
	 *    color while the active tab used the shared light background
	 *    (`@vc_tta-common-background-color`, i.e. `#f8f8f8`).
	 *
	 * 9.0 splits this in two params (`color` for the inactive state,
	 * `active_color` for the active state) and the new `active_color` registers a
	 * non-empty `std` (#F8F8F8) that matches the legacy common background.
	 *
	 * To preserve the legacy look for elements saved before the split we must:
	 *  - For `flat`: mirror the resolved `color` value into `active_color` so the
	 *    active tab keeps matching the inactive one.
	 *  - For every other style: leave `active_color` untouched so `shortcode_atts()`
	 *    injects the `#F8F8F8` std, which reproduces the original active tint.
	 *
	 * Brand-new elements that explicitly save `active_color` are left untouched.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_tabs_active_color_to_color( $out, $pairs, $atts ) {
		if ( array_key_exists( 'active_color', (array) $atts ) ) {
			return $out;
		}

		$style = isset( $out['style'] ) ? (string) $out['style'] : '';
		if ( 'flat' !== $style ) {
			return $out;
		}

		if ( ! empty( $out['color'] ) ) {
			$out['active_color'] = $out['color'];
		}

		return $out;
	}

	/**
	 * Migrate count textfield to number with toggle for none numeric values.
	 *
	 * Prior to 9.0 we used textfield for count attr that allow users set 'all' value.
	 * Right now we replaced it with number and toggle 'display_all'
	 *
	 * @param array $out    Merged shortcode attributes (registered keys only).
	 * @param array $pairs  Registered attribute defaults.
	 * @param array $atts   Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_posts_slider_count_textfield_to_number( $out, $pairs, $atts ) {
		if ( isset( $atts['count'] ) ) {
			if ( is_numeric( $atts['count'] ) ) {
				$out['display_all'] = '';
			} else {
				$out['display_all'] = 'yes';
				unset( $out['count'] );
			}
		}

		return $out;
	}

	/**
	 * Convert elements with integrated button to new button colors param.
	 *
	 * @param array $out Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts Original raw shortcode attributes.
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function convert_integrated_btn( $out, $pairs, $atts, $prefix = 'btn_' ) {
		$out = $this->convert_btn_dropdown_color_to_custom( $out, $pairs, $atts, $prefix );
		$out = $this->convert_btn_outline_dropdown_color_to_custom( $out, $pairs, $atts, $prefix );
		$out = $this->convert_btn_3d_dropdown_color_to_custom( $out, $pairs, $atts, $prefix );
		$out = $this->convert_btn_default_outline_dropdown_color_to_custom( $out, $pairs, $atts, $prefix );
		$out = $this->convert_btn_default_3d_dropdown_color_to_custom( $out, $pairs, $atts, $prefix );
		return $this->convert_btn_gradient_style_to_gradient_custom( $out, $pairs, $atts, $prefix );
	}

	/**
	 * Migrate legacy options checkbox to separate type dropdown and count toggle attributes.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_wp_archives_options_to_type_and_count( $out, $pairs, $atts ) {
		return $this->migrate_wp_archives_options_to_type_and_count( $out, $atts );
	}

	/**
	 * Migrate legacy options checkbox to separate item_content/item_author/item_date toggle attributes.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_wp_rss_options_checkbox_to_toggles( $out, $pairs, $atts ) {
		return $this->migrate_wp_rss_options_to_toggles( $out, $atts );
	}

	/**
	 * Convert vc_hoverbox integrated button to new params.
	 *
	 * @param array $out Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_vc_hoverbox_integrated_btn( $out, $pairs, $atts ) {
		return $this->convert_integrated_btn( $out, $pairs, $atts, 'hover_btn_' );
	}

	/**
	 * Backward compatibility for the renamed `content_placement` param of vc_section.
	 *
	 * Prior to 9.0, vc_section's vertical content position param was named
	 * `content_placement`. It is now `vertical_content_position`. Elements saved
	 * under the old name keep rendering correctly until they are re-saved.
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_section_content_placement_to_vertical_content_position( $out, $pairs, $atts ) {
		return $this->migrate_renamed_attribute( $out, (array) $atts, 'content_placement', 'vertical_content_position' );
	}

	/**
	 * Migrate the `autoplay` default value from 0 to 7 for vc_tta_pageable elements.
	 *
	 * @param array $out   The output array of shortcode attributes.
	 * @param array $pairs The supported attributes and their defaults.
	 * @param array $atts  The user-defined shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function update_tta_pageable_autoplay_default( $out, $pairs, $atts ) {
		if ( ! isset( $atts['autoplay'] ) ) {
			$out['autoplay'] = '0';
		}

		return $out;
	}

	/**
	 * Map legacy outline `color` to the dedicated outline color and title colors.
	 *
	 * Before the outline redesign a single `color` drove the outline border and
	 * title text. The border now comes from `outline_color` and the title from
	 * the title colors, so legacy outline accordions would otherwise lose their
	 * colour. Only runs when `color` was explicitly saved (legacy content).
	 *
	 * @param array $out   Merged shortcode attributes (registered keys only).
	 * @param array $pairs Registered attribute defaults.
	 * @param array $atts  Original raw shortcode attributes.
	 * @return array
	 * @since 9.0
	 */
	public function convert_tta_accordion_outline_color( $out, $pairs, $atts ) {
		$raw_color = (string) ( $atts['color'] ?? '' );
		$style = (string) ( $out['style'] ?? '' );
		$color = (string) ( $out['color'] ?? '' );
		if ( '' === $raw_color || 'outline' !== $style || '' === $color ) {
			return $out;
		}

		foreach ( [ 'outline_color', 'inactive_title_color', 'active_title_color' ] as $key ) {
			if ( empty( $out[ $key ] ) ) {
				$out[ $key ] = $color;
			}
		}

		return $out;
	}

	/**
	 * Inject legacy `autoplay="0"` into vc_tta_pageable shortcodes for the editor.
	 *
	 * Keeps content saved without `autoplay` (legacy "none" = 0) from being
	 * rewritten to the new `save_always` default of 7 on the next save.
	 *
	 * @param string $content Post content shown in the editor textarea.
	 * @return string
	 * @since 9.0
	 */
	public function backfill_tta_pageable_autoplay_in_editor( $content ) {
		if ( ! is_string( $content ) || false === strpos( $content, '[vc_tta_pageable' ) ) {
			return $content;
		}

		return preg_replace_callback(
			'/\[vc_tta_pageable\b([^\]]*)\]/',
			function ( $matches ) {
				if ( false !== strpos( $matches[1], 'autoplay' ) ) {
					return $matches[0];
				}

				return '[vc_tta_pageable autoplay="0"' . $matches[1] . ']';
			},
			$content
		);
	}

	/**
	 * Inject legacy params removed from main config but kept for the migration process.
	 *
	 * @since 9.0
	 */
	public function register_legacy_params() {
		$legacy_params = [
			[
				'element' => 'vc_progress_bar',
				'param_name' => 'bgcolor',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_round_chart',
				'param_name' => 'stroke_color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_round_chart',
				'param_name' => 'legend_color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_pie',
				'param_name' => 'color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_cta',
				'param_name' => 'color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_btn',
				'param_name' => 'color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_btn',
				'param_name' => 'gradient_color_1',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_btn',
				'param_name' => 'gradient_color_2',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_gitem_image',
				'param_name' => 'border_color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_gitem_post_categories',
				'param_name' => 'category_color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_separator',
				'param_name' => 'color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_zigzag',
				'param_name' => 'color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_icon',
				'param_name' => 'color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_icon',
				'param_name' => 'background_color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_text_separator',
				'param_name' => 'i_color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_cta',
				'param_name' => 'i_color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_text_separator',
				'param_name' => 'i_background_color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_cta',
				'param_name' => 'i_background_color',
				'deprecated' => '9.0',
			],
			[
				'element' => 'vc_hoverbox',
				'param_name' => 'hover_background_color',
				'deprecated' => '9.0',
			],
		];

		$legacy_params = apply_filters( 'wpb_element_legacy_params', $legacy_params );

		foreach ( $legacy_params as $legacy_param ) {
			vc_add_param( $legacy_param['element'], [
				'type'       => 'hidden',
				'param_name' => $legacy_param['param_name'],
				'value'      => '',
				'deprecated' => $legacy_param['deprecated'],
			] );
		}
	}
}
