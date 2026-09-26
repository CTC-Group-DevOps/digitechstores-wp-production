<?php
/**
 * Param type 'link'.
 *
 * Unified link input with inline search and popup editor.
 * Replaces both 'href' (plain URL) and 'vc_link' (WordPress wpLink modal).
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WpbParamLink
 *
 * @since 9.0
 */
class WpbParamLink {
	/**
	 * Initialize hooks.
	 *
	 * @since 9.0
	 */
	public function init() {
		add_action( 'wp_ajax_wpb_link_search', [ $this, 'link_search_ajax' ], );
		add_action( 'wp_ajax_wpb_attachment_title', [ $this, 'attachment_title_ajax' ] );
	}

	/**
	 * Render param's HTML.
	 *
	 * @param array $settings Param settings.
	 * @param string $value Param value.
	 * @param string $tag WPBakery shortcode tag.
	 * @param string $param_id Param ID.
	 *
	 * @return string
	 */
	public function render( $settings, $value, $tag = '', $param_id = '' ) {
		// B.C for href param type conversion.
		if ( ! empty( $value ) ) {
			$link_par_list = [ 'url:', 'target:', 'rel:', 'title:' ];
			$is_link_param_type = preg_match( '/' . implode( '|', $link_par_list ) . '/', $value );

			if ( ! $is_link_param_type ) {
				$value = 'url:' . rawurlencode( $value );
			}
		}

		$link = vc_build_link( $value );

		return WPB_Form_Field_Link::get([ // nosemgrep - escaping handled by WPB_Form_Field_Autocomplete.
			'value'    => $value,
			'link'     => $link,
			'name'     => $settings['param_name'],
			'type'     => $settings['type'],
			'settings' => $settings['settings'] ?? [],
			'id'       => wpbakery()->editForm()->get_value_control_id( $param_id, $settings['type'] ),
		]);
	}


	/**
	 * AJAX handler for link param post/page search.
	 *
	 * Returns up to 10 posts matching the search query,
	 * with title, permalink, and post type for each result.
	 *
	 * @since 9.0
	 */
	public function link_search_ajax() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

		$query = sanitize_text_field( vc_post_param( 'query' ) );

		if ( strlen( $query ) < 2 ) {
			wp_send_json_success( [] );
		}

		$search_results = $this->get_search_results( $query );

		foreach ( $search_results as $key => $result ) {
			if ( 'attachment' === $result['type'] ) {
				$search_results[ $key ]['url'] = get_permalink( $result['id'] );
			} elseif ( taxonomy_exists( $result['type'] ) ) {
				$term_link = get_term_link( (int) $result['id'], $result['type'] );
				$search_results[ $key ]['url'] = is_wp_error( $term_link ) ? '' : $term_link;
			} else {
				$search_results[ $key ]['url'] = get_permalink( $result['id'] );
			}
		}

		wp_send_json_success( $search_results );
	}

	/**
	 * Get search results for posts and terms matching the query.
	 *
	 * @param string $query
	 * @return array Search results with id, title, slug, type, and URL.
	 */
	public function get_search_results( $query ) {
		global $wpdb;

		$like = '%' . $wpdb->esc_like( $query ) . '%';

		$internal_post_types = get_post_types( [
			'_builtin' => true,
			'public'   => false,
		] );

		$excluded_types = "'" . implode( "','", array_map( 'esc_sql', $internal_post_types ) ) . "'";

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql = $wpdb->prepare(
			"
		SELECT ID as id, post_title as title, post_name as slug, post_type as type
		FROM {$wpdb->posts}
		WHERE (post_status = 'publish' OR post_status = 'inherit')
		AND post_type NOT IN ($excluded_types)
		AND (post_title LIKE %s OR post_name LIKE %s)

		UNION

		SELECT t.term_id as id, t.name, t.slug, tt.taxonomy as type
		FROM {$wpdb->terms} t
		JOIN {$wpdb->term_taxonomy} tt
			ON t.term_id = tt.term_id
		WHERE (t.name LIKE %s OR t.slug LIKE %s)
		LIMIT 10
		",
			$like,
			$like,
			$like,
			$like
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		// phpcs:ignore WordPress.DB
		return $this->apply_instance_name( $wpdb->get_results( $sql, ARRAY_A ) );
	}
	/**
	 * Replace post or taxonomy slugs in results with human-readable labels.
	 *
	 * @param array $results
	 * @return array
	 */
	private function apply_instance_name( array $results ) {
		return array_map( function ( $result ) {
			if ( taxonomy_exists( $result['type'] ) ) {
				$taxonomy_obj       = get_taxonomy( $result['type'] );
				$result['name'] = $taxonomy_obj ? $taxonomy_obj->labels->singular_name : $result['type'];
			} else {
				$post_type_object = get_post_type_object( $result['type'] );
				$result['name'] = $post_type_object ? $post_type_object->labels->singular_name : $result['type'];
			}

			return $result;
		}, $results );
	}

	/**
	 * AJAX handler to get an attachment's title by ID.
	 *
	 * Used by the href link param to auto-populate title
	 * from the WordPress media library attachment.
	 *
	 * @since 9.0
	 */
	public function attachment_title_ajax() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

		$attachment_id = (int) vc_post_param( 'attachment_id' );

		if ( ! $attachment_id ) {
			wp_send_json_success( [ 'title' => '' ] );
		}

		$attachment = get_post( $attachment_id );
		$title      = $attachment ? trim( wp_strip_all_tags( $attachment->post_title ) ) : '';

		wp_send_json_success( [ 'title' => $title ] );
	}
}

$link = new WpbParamLink();
$link->init();
