<?php
/**
 * Plugin elements lean mapping hooks
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPB_Lean_Map_Hooks class.
 *
 * @since 9.0
 */
class WPB_Lean_Map_Hooks {
	/**
	 * Initialize hooks.
	 *
	 * @since 9.0
	 */
	public function init() {
		add_filter( 'vc_element_settings_filter', [ $this, 'arrange_advanced_section' ], 10, 2 );

		if ( is_admin() ) {
			add_action( 'admin_print_scripts-post.php', [
				Vc_Shortcodes_Manager::getInstance(),
				'buildShortcodesAssets',
			], 1 );
			add_action( 'admin_print_scripts-post-new.php', [
				Vc_Shortcodes_Manager::getInstance(),
				'buildShortcodesAssets',
			], 1 );
			add_action( 'vc-render-templates-preview-template', [
				Vc_Shortcodes_Manager::getInstance(),
				'buildShortcodesAssets',
			], 1 );
		} elseif ( vc_is_page_editable() ) {
			add_action( 'wp_head', [
				Vc_Shortcodes_Manager::getInstance(),
				'buildShortcodesAssetsForEditable',
			] ); // @todo where these icons are used in iframe?
		}

		require_once vc_path_dir( 'CONFIG_DIR', 'grids/vc-grids-functions.php' );
		if ( 'vc_get_autocomplete_suggestion' === vc_request_param( 'action' ) || 'vc_edit_form' === vc_post_param( 'action' ) ) {
			add_filter( 'vc_autocomplete_vc_basic_grid_include_callback', 'vc_include_field_search' ); // Get suggestion(find). Must return an array.
			add_filter( 'vc_autocomplete_vc_basic_grid_include_render', 'vc_include_field_render' ); // Render exact product. Must return an array (label,value).
			add_filter( 'vc_autocomplete_vc_masonry_grid_include_callback', 'vc_include_field_search' ); // Get suggestion(find). Must return an array.
			add_filter( 'vc_autocomplete_vc_masonry_grid_include_render', 'vc_include_field_render' ); // Render exact product. Must return an array (label,value).

			// Narrow data taxonomies.
			add_filter( 'vc_autocomplete_vc_basic_grid_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search' );
			add_filter( 'vc_autocomplete_vc_basic_grid_taxonomies_render', 'vc_autocomplete_taxonomies_field_render' );

			add_filter( 'vc_autocomplete_vc_masonry_grid_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search' );
			add_filter( 'vc_autocomplete_vc_masonry_grid_taxonomies_render', 'vc_autocomplete_taxonomies_field_render' );

			// Narrow data taxonomies for exclude_filter.
			add_filter( 'vc_autocomplete_vc_basic_grid_exclude_filter_callback', 'vc_autocomplete_taxonomies_field_search' );
			add_filter( 'vc_autocomplete_vc_basic_grid_exclude_filter_render', 'vc_autocomplete_taxonomies_field_render' );

			add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_filter_callback', 'vc_autocomplete_taxonomies_field_search' );
			add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_filter_render', 'vc_autocomplete_taxonomies_field_render' );

			add_filter( 'vc_autocomplete_vc_basic_grid_exclude_callback', 'vc_exclude_field_search' ); // Get suggestion(find). Must return an array.
			add_filter( 'vc_autocomplete_vc_basic_grid_exclude_render', 'vc_exclude_field_render' ); // Render exact product. Must return an array (label,value).
			add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_callback', 'vc_exclude_field_search' ); // Get suggestion(find). Must return an array.
			add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_render', 'vc_exclude_field_render' ); // Render exact product. Must return an array (label,value).
		}
	}

	/**
	 * Get a list of plugin elements that has the advanced section.
	 *
	 * @since 9.0
	 */
	public function get_advanced_section_elements() {
		return apply_filters( 'wpb_advanced_section_elements', [
			'vc_cta',
			'vc_row_inner',
			'vc_pricing_table',
			'vc_basic_grid',
			'vc_masonry_grid',
			'vc_media_grid',
			'vc_masonry_media_grid',
			'vc_message',
			'vc_toggle',
			'vc_btn',
			'vc_custom_heading',
			'vc_row',
			'vc_column',
			'vc_column_inner',
			'vc_section',
			'vc_flexbox_container',
			'vc_flexbox_container_item',
			'vc_grid_container',
			'vc_grid_container_item',
			'vc_posts_slider',
			'vc_empty_space',
			'vc_column_text',
			'vc_gutenberg',
			'vc_separator',
			'vc_text_separator',
			'vc_single_image',
			'vc_gallery',
			'vc_flickr',
			'vc_icon',
			'vc_zigzag',
			'vc_pie',
			'vc_round_chart',
			'vc_line_chart',
			'vc_video',
			'vc_progress_bar',
			'vc_images_carousel',
			'vc_goo_maps',
			'vc_copyright',
			'vc_tta_accordion',
			'vc_tta_tabs',
			'vc_tta_tour',
			'vc_tta_toggle',
			'vc_tta_pageable',
			'vc_tta_section',
			'vc_tta_toggle_section',
			'vc_facebook',
			'vc_pinterest',
			'vc_tweetmeme',
			'vc_widget_sidebar',
			'vc_raw_js',
			'vc_raw_html',
			'vc_wp_calendar',
			'vc_wp_tagcloud',
			'vc_wp_rss',
			'vc_wp_archives',
			'vc_wp_text',
			'vc_wp_posts',
			'vc_wp_pages',
			'vc_wp_custommenu',
			'vc_wp_meta',
			'vc_wp_categories',
			'vc_wp_search',
			'vc_wp_recentcomments',
			'vc_wp_links',
			'vc_hoverbox',
			'vc_gitem_post_excerpt',
			'vc_gitem_post_title',
			'vc_gitem_post_author',
			'vc_gitem_post_categories',
			'vc_gitem_image',
			'vc_gitem_post_date',
			'vc_gitem_post_meta',
		]);
	}

	/**
	 * Move the advanced section under the general.
	 *
	 * @since 9.0
	 * @param array $settings
	 * @param string $tag
	 */
	public function arrange_advanced_section( $settings, $tag ) {
		$list = $this->get_advanced_section_elements();
		$has_advanced_params = in_array( $tag, $list, true );
		$has_sections = isset( $settings['sections'] );

		if ( $has_advanced_params && ! $has_sections ) {
			$settings = vc_config()->attach_advanced_section_to_params( $settings );
		}

		return $settings;
	}
}

$lean_map = new WPB_Lean_Map_Hooks();
$lean_map->init();
