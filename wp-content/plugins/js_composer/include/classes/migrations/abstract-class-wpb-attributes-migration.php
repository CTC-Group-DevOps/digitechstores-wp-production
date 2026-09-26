<?php
/**
 * Abstract attributes migrations class
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Wpb_Attributes_Migration_Abstract
 *
 * @since 9.0
 */
abstract class Wpb_Attributes_Migration_Abstract {
	/**
	 * Init hooks.
	 *
	 * @since 9.0
	 */
	abstract public function init();

	/**
	 * Applies flat color map values to CTA custom_* attributes.
	 *
	 * @param array  $out
	 * @param array  $atts
	 * @param string $color Slug key into cta-colors map.
	 * @param bool $is_template
	 * @return array
	 * @since 9.0
	 */
	public function apply_cta_flat_color( array $out, array $atts, string $color, $is_template = false ): array {
		$color_map = vc_get_shared( 'cta-colors' );

		if ( ! isset( $color_map[ $color ] ) ) {
			return $atts;
		}

		[ $text, $background, $heading ] = $color_map[ $color ];

		if ( ! isset( $atts['custom_text'] ) ) {
			$out['custom_text'] = $heading;
			if ( $is_template ) {
				$out['text_color'] = $text;
			}
		}
		if ( ! isset( $atts['custom_background'] ) ) {
			$out['custom_background'] = $background;
		}

		return $out;
	}

	/**
	 * Applies outline color map values to CTA custom_* attributes.
	 *
	 * Both custom_text (heading) and custom_border equal the main @color value.
	 *
	 * @param array  $out
	 * @param array  $atts
	 * @param string $color Slug key into cta-colors map.
	 * @return array
	 * @since 9.0
	 */
	public function apply_cta_outline_color( array $out, array $atts, string $color ): array {
		$color_map = vc_get_shared( 'cta-colors' );

		if ( ! isset( $color_map[ $color ] ) ) {
			return $atts;
		}

		[ , $background ] = $color_map[ $color ];

		if ( ! isset( $atts['custom_text'] ) ) {
			$out['custom_text'] = $background;
		}
		if ( ! isset( $atts['custom_border'] ) ) {
			$out['custom_border'] = $background;
		}

		return $out;
	}

	/**
	 * Applies 3d color map values to CTA custom_* attributes.
	 *
	 * @param array  $out
	 * @param array  $atts
	 * @param string $color Slug key into cta-colors map.
	 * @param bool $is_template
	 * @return array
	 * @since 9.0
	 */
	public function apply_cta_3d_color( array $out, array $atts, string $color, $is_template = false ): array {
		$color_map = vc_get_shared( 'cta-colors' );

		if ( ! isset( $color_map[ $color ] ) ) {
			return $atts;
		}

		[ $text, $background, $heading, $shadow ] = $color_map[ $color ];

		if ( ! isset( $atts['custom_text'] ) ) {
			$out['custom_text'] = $heading;
			if ( $is_template ) {
				$out['text_color'] = $text;
			}
		}
		if ( ! isset( $atts['custom_background'] ) ) {
			$out['custom_background'] = $background;
		}
		if ( ! isset( $atts['custom_border'] ) ) {
			$out['custom_border'] = $shadow;
		}

		return $out;
	}

	/**
	 * Applies solid color map values to button custom_* attributes.
	 *
	 * @param array  $atts
	 * @param string $color  Slug key into btn-solid-colors map.
	 * @param string $style  Button style (modern/classic/flat).
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function apply_btn_solid_color( array $atts, string $color, string $style, string $prefix = '' ): array {
		$color_map = vc_get_shared( 'btn-solid-colors' );

		if ( ! isset( $color_map[ $color ] ) ) {
			return $atts;
		}

		[ $background, $text, $hover, $hover_text ] = $color_map[ $color ];

		$atts[ $prefix . 'custom_background' ]       = $background;
		$atts[ $prefix . 'custom_text' ]             = $text;
		$atts[ $prefix . 'custom_hover_background' ] = $hover;
		$atts[ $prefix . 'custom_hover_text' ]       = $hover_text;

		if ( 'modern' === $style ) {
			$atts[ $prefix . 'custom_border' ]       = $background;
			$atts[ $prefix . 'custom_hover_border' ] = $hover;
		}

		unset( $atts[ $prefix . 'color' ] );

		return $atts;
	}

	/**
	 * Applies 3d color map values to button custom_* attributes.
	 *
	 * @param array  $atts
	 * @param string $color Slug key into btn-3d-colors map.
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function apply_btn_3d_color( array $atts, string $color, string $prefix = '' ): array {
		$color_map = vc_get_shared( 'btn-3d-colors' );

		if ( ! isset( $color_map[ $color ] ) ) {
			return $atts;
		}

		[ $background, $text, $shadow ] = $color_map[ $color ];

		$atts[ $prefix . 'custom_background' ] = $background;
		$atts[ $prefix . 'custom_text' ]       = $text;
		$atts[ $prefix . 'custom_border' ]     = $shadow;

		unset( $atts[ $prefix . 'color' ] );

		return $atts;
	}

	/**
	 * Applies outline color map values to button custom_* attributes.
	 *
	 * @param array  $atts
	 * @param string $color Slug key into btn-outline-colors map.
	 * @param string $prefix
	 * @return array
	 * @since 9.0
	 */
	public function apply_btn_outline_color( array $atts, string $color, string $prefix = '' ): array {
		$color_map = vc_get_shared( 'btn-outline-colors' );

		if ( ! isset( $color_map[ $color ] ) ) {
			return $atts;
		}

		[ $text, $border, $hover_text ] = $color_map[ $color ];

		$atts[ $prefix . 'custom_text' ]             = $text;
		$atts[ $prefix . 'custom_border' ]           = $border;
		$atts[ $prefix . 'custom_hover_background' ] = $border;
		$atts[ $prefix . 'custom_hover_text' ]       = $hover_text;
		$atts[ $prefix . 'custom_hover_border' ]     = $border;

		unset( $atts[ $prefix . 'color' ] );

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
		return empty( $atts[ $target_key ] ) && ! empty( $atts[ $source_key ] ) && 'custom' !== $atts[ $source_key ];
	}

	/**
	 * Resolve a dropdown color slug to its hex value via the colors-hash map.
	 *
	 * Returns the mapped hex when the slug exists in the map, or the raw slug value otherwise.
	 *
	 * @param array  $atts
	 * @param string $source_key
	 * @param array $hash_lib
	 * @return string
	 * @since 9.0
	 */
	protected function resolve_dropdown_color( array $atts, string $source_key, $hash_lib ): string {
		return array_key_exists( $atts[ $source_key ], $hash_lib ) ? $hash_lib[ $atts[ $source_key ] ] : (string) $atts[ $source_key ];
	}

	/**
	 * Migrates a predefined dropdown color attribute to a custom colorpicker attribute.
	 *
	 * Resolves the stored slug via the colors-hash map and writes the hex value into $out.
	 * When $unset_source is true the source key is also removed from $out (used by the edit
	 * form context where $out and $atts are the same array).
	 *
	 * @param array  $out          Target attribute array to receive the resolved color.
	 * @param array  $atts         Source attribute array to read the dropdown value from.
	 * @param string $source_key   Attribute holding the old dropdown color value.
	 * @param string $target_key   Attribute that should receive the resolved hex color.
	 * @param bool   $unset_source Whether to remove the source key from $out after migration.
	 * @param array  $hash_lib     Color hash lib.
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_dropdown_color_to_custom( array $out, array $atts, string $source_key, string $target_key, bool $unset_source = false, array $hash_lib = [] ): array {
		if ( $this->should_migrate_dropdown_color( $atts, $source_key, $target_key ) ) {
			if ( ! $hash_lib ) {
				$hash_lib = vc_get_shared( 'colors-hash' );
			}
			$out[ $target_key ] = $this->resolve_dropdown_color( $atts, $source_key, $hash_lib );
			if ( $unset_source ) {
				unset( $out[ $source_key ] );
			}
		}
		return $out;
	}

	/**
	 * Resolve a legacy dropdown color slug to its colorpicker hex value in place.
	 *
	 * Unlike {@see migrate_dropdown_color_to_custom()} this helper does not introduce
	 * a separate target attribute — the same key is overwritten with the resolved hex.
	 * Used by shortcodes whose dropdown was replaced by a colorpicker without renaming
	 * the attribute (e.g. vc_tta_tabs `color`, `pagination_color`). Values that already
	 * look like a custom color (hex / rgb / hsl) are left untouched.
	 *
	 * @param array  $atts
	 * @param string $key
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_inline_dropdown_color_to_custom( array $atts, string $key ): array {
		if ( empty( $atts[ $key ] ) ) {
			return $atts;
		}

		$value = (string) $atts[ $key ];
		if ( preg_match( '/^(#|rgb|hsl)/i', $value ) ) {
			return $atts;
		}

		$hash_lib = vc_get_shared( 'colors-hash' );

		// The legacy dropdown stored dashed slugs (vista-blue, mulled-wine, juicy-pink,
		// sandy-brown) but the hash map is keyed with underscores. Try the dashed form
		// first, then fall back to the underscore form, then to the raw value.
		if ( array_key_exists( $value, $hash_lib ) ) {
			$atts[ $key ] = $hash_lib[ $value ];
		} else {
			$normalised = str_replace( '-', '_', $value );
			if ( $normalised !== $value && array_key_exists( $normalised, $hash_lib ) ) {
				$atts[ $key ] = $hash_lib[ $normalised ];
			}
		}

		return $atts;
	}

	/**
	 * Migrate legacy 'custom' style value to 'flat' for vc_line_chart.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function migrate_line_chart_style_custom_to_flat( array $atts ): array {
		if ( isset( $atts['style'] ) && 'custom' === $atts['style'] ) {
			$atts['style'] = 'flat';
		}
		return $atts;
	}

	/**
	 * Migrate legacy 'custom' style value to 'flat' for vc_round_chart.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function migrate_round_chart_style_custom_to_flat( array $atts ): array {
		if ( isset( $atts['style'] ) && 'custom' === $atts['style'] ) {
			$atts['style'] = 'flat';
		}
		return $atts;
	}

	/**
	 * Parse chart values param group, migrate per-item color dropdown to custom_color, and re-encode.
	 *
	 * @param array $atts Element attribute array containing the encoded values param group.
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_chart_values_color( array $atts ): array {
		if ( empty( $atts['values'] ) ) {
			return $atts;
		}

		$items = vc_param_group_parse_atts( $atts['values'] );
		if ( ! is_array( $items ) ) {
			return $atts;
		}

		$style_is_custom = isset( $atts['style'] ) && 'custom' === $atts['style'];
		$hash_lib = vc_get_shared( 'dashed-colors-hash' );
		$changed = false;
		foreach ( $items as $index => $item ) {
			if ( ! isset( $item['color'] ) ) {
				continue;
			}

			$items[ $index ] = $this->migrate_chart_item_color( $item, $style_is_custom, $hash_lib );
			$changed = true;
		}

		if ( $changed ) {
			$atts['values'] = rawurlencode( wp_json_encode( $items ) );
		}

		return $atts;
	}

	/**
	 * Resolve a single chart value item's color to the custom_color colorpicker.
	 *
	 * @param array $item
	 * @param bool  $style_is_custom
	 * @param array $hash_lib
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_chart_item_color( array $item, bool $style_is_custom, array $hash_lib ): array {
		if ( $style_is_custom ) {
			if ( empty( $item['custom_color'] ) ) {
				$item['custom_color'] = $hash_lib['grey'];
			}
		} elseif ( 'custom' !== $item['color'] ) {
			$item['custom_color'] = $this->resolve_dropdown_color( $item, 'color', $hash_lib );
		}

		unset( $item['color'] );

		return $item;
	}

	/**
	 * Parse values param group, migrate per-bar color dropdown to customcolor/customtxtcolor, and re-encode.
	 *
	 * @param string $raw_values Encoded values param group string.
	 * @return string Re-encoded string, or original if nothing changed.
	 * @since 9.0
	 */
	protected function migrate_progress_bar_values_color( string $raw_values ): string {
		$items = vc_param_group_parse_atts( $raw_values );
		if ( ! is_array( $items ) ) {
			return $raw_values;
		}

		$changed = false;
		foreach ( $items as $index => $bar ) {
			if ( ! isset( $bar['color'] ) ) {
				continue;
			}

			$bar = $this->apply_progress_bar_color( $bar, $bar['color'], 'customcolor', 'customtxtcolor' );
			unset( $bar['color'] );
			$items[ $index ] = $bar;
			$changed = true;
		}

		return $changed ? rawurlencode( wp_json_encode( $items ) ) : $raw_values;
	}

	/**
	 * Resolve a legacy progress bar color slug to a hex value.
	 *
	 * Handles the classic bar_* slugs and all standard vc color slugs via vc_convert_vc_color.
	 *
	 * @param string $slug
	 * @return string Hex color or empty string for unknown/default.
	 * @since 9.0
	 */
	protected function resolve_progress_bar_color( string $slug ): string {
		$bar_colors = [
			'bar_blue'      => '#0074CC',
			'bar_turquoise' => '#49afcd',
			'bar_green'     => '#5bb75b',
			'bar_orange'    => '#faa732',
			'bar_red'       => '#da4f49',
			'bar_black'     => '#414141',
			'bar_grey'      => '',
		];

		if ( array_key_exists( $slug, $bar_colors ) ) {
			return $bar_colors[ $slug ];
		}

		return vc_convert_vc_color( $slug );
	}

	/**
	 * Resolve the legacy progress bar label text color for a given background color slug.
	 *
	 * The progress bar LESS mixin defaults text to #ffffff; only grey and white use #666666.
	 * Returns empty string for no-color slugs (empty / bar_grey).
	 *
	 * @param string $slug
	 * @return string Hex color or empty string.
	 * @since 9.0
	 */
	protected function resolve_progress_bar_txt_color( string $slug ): string {
		if ( '' === $slug || 'bar_grey' === $slug ) {
			return '';
		}

		if ( in_array( str_replace( '_', '-', $slug ), [ 'grey', 'white' ], true ) ) {
			return '#666666';
		}

		return '#ffffff';
	}

	/**
	 * Migrate legacy options checkbox to separate type dropdown and count toggle attributes.
	 *
	 * Reads the old comma-separated options param from $raw and writes the resolved values
	 * into $out. Only sets type/count when not already present in $raw, so explicitly
	 * saved new values are never overwritten.
	 *
	 * @param array $out Destination attribute array to receive the new values.
	 * @param array $raw Source attribute array containing the legacy options param.
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_wp_archives_options_to_type_and_count( array $out, array $raw ): array {
		if ( ! isset( $raw['options'] ) ) {
			return $out;
		}

		$options = array_filter( array_map( 'trim', explode( ',', $raw['options'] ) ) );

		$type = in_array( 'dropdown', $options, true ) ? 'dropdown' : 'list';
		if ( is_array( $raw['type'] ) ) {
			$out['type']['value'] = $type;
		} else {
			$out['type'] = $type;
		}

		if ( empty( $raw['count'] ) && in_array( 'count', $options, true ) ) {
			$out['count'] = 'true';
		}

		unset( $out['options'] );

		return $out;
	}

	/**
	 * Migrate legacy options checkbox values to separate striped/animated toggle attributes.
	 *
	 * Reads the old comma-separated options param from $raw and writes the resolved toggle
	 * values into $out. Only sets a toggle when it is not already present in $raw, so
	 * explicitly saved new values are never overwritten.
	 *
	 * @param array $out Destination attribute array to receive the toggle values.
	 * @param array $raw Source attribute array containing the legacy options param.
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_progress_bar_options_to_toggles( array $out, array $raw ): array {
		if ( ! isset( $raw['options'] ) ) {
			return $out;
		}

		$options = array_filter( array_map( 'trim', explode( ',', $raw['options'] ) ) );

		if ( empty( $raw['striped'] ) && in_array( 'striped', $options, true ) ) {
			$out['striped'] = 'true';
		}

		if ( empty( $raw['animated'] ) && in_array( 'animated', $options, true ) ) {
			$out['animated'] = 'true';
		}

		unset( $out['options'] );

		return $out;
	}

	/**
	 * Resolve a legacy color slug and write the background + text hex values into $data.
	 *
	 * Only writes when the target keys are still empty, so existing values are never overwritten.
	 *
	 * @param array  $data    Attribute array to update (element atts or per-bar array).
	 * @param string $slug    Legacy dropdown color slug.
	 * @param string $bg_key  Key to receive the resolved background hex.
	 * @param string $txt_key Key to receive the resolved text hex.
	 * @return array
	 * @since 9.0
	 */
	protected function apply_progress_bar_color( array $data, string $slug, string $bg_key, string $txt_key ): array {
		if ( 'custom' === $slug || '' === $slug || ! empty( $data[ $bg_key ] ) ) {
			return $data;
		}

		$data[ $bg_key ] = $this->resolve_progress_bar_color( $slug );

		if ( ! empty( $data[ $bg_key ] ) ) {
			$data[ $txt_key ] = $this->resolve_progress_bar_txt_color( $slug );
			$data['add_text_shadow'] = 'true';
			$data['text_shadow_color'] = '#00000040';
		}

		return $data;
	}

	/**
	 * Migrate legacy options checkbox values to separate item_content/item_author/item_date toggle attributes.
	 *
	 * Reads the old comma-separated options param from $raw and writes the resolved toggle
	 * values into $out. Only sets a toggle when it is not already present in $raw, so
	 * explicitly saved new values are never overwritten.
	 *
	 * @param array $out Destination attribute array to receive the toggle values.
	 * @param array $raw Source attribute array containing the legacy options param.
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_wp_rss_options_to_toggles( array $out, array $raw ): array {
		if ( ! isset( $raw['options'] ) ) {
			return $out;
		}

		$options = array_filter( array_map( 'trim', explode( ',', $raw['options'] ) ) );

		if ( empty( $raw['item_content'] ) && in_array( 'show_summary', $options, true ) ) {
			$out['item_content'] = 'true';
		}

		if ( empty( $raw['item_author'] ) && in_array( 'show_author', $options, true ) ) {
			$out['item_author'] = 'true';
		}

		if ( empty( $raw['item_date'] ) && in_array( 'show_date', $options, true ) ) {
			$out['item_date'] = 'true';
		}

		unset( $out['options'] );

		return $out;
	}

	/**
	 * Restore the legacy default of an attribute when it is missing from the raw saved atts.
	 *
	 * Used when a param's `std` value changes between versions (for example a checkbox
	 * that defaulted to "off" being replaced by a toggle that defaults to "on"). Legacy
	 * elements saved without the attribute would otherwise inherit the new default after
	 * `shortcode_atts()` merges, silently changing their behaviour. This helper rewrites
	 * the attribute in $out back to its previous default whenever the raw atts ($raw) do
	 * not contain the key, leaving explicitly saved values untouched.
	 *
	 * @param array  $out             Target attribute array to update (merged atts).
	 * @param array  $raw             Original raw shortcode atts (pre-merge).
	 * @param string $key             Attribute name to migrate.
	 * @param string $legacy_default  Value the attribute had before the std changed.
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_missing_attribute_to_legacy_default( array $out, array $raw, string $key, string $legacy_default ): array {
		if ( ! array_key_exists( $key, $raw ) ) {
			$out[ $key ] = $legacy_default;
		}

		return $out;
	}

	/**
	 * Check whether a value is a legacy CTA position slug.
	 *
	 * @param string $value
	 * @return bool
	 * @since 9.0
	 */
	protected function is_cta_legacy_position( string $value ): bool {
		return in_array( $value, [ 'top', 'bottom', 'left', 'right' ], true );
	}

	/**
	 * Migrate CTA el_width dropdown slugs to numeric percentage for the range param.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_el_width_dropdown_to_range( array $atts ): array {
		if ( ! isset( $atts['el_width'] ) ) {
			return $atts;
		}

		$slug_to_percent = [
			''   => '100',
			'xl' => '90',
			'lg' => '80',
			'md' => '70',
			'sm' => '60',
			'xs' => '50',
		];

		if ( ! array_key_exists( $atts['el_width'], $slug_to_percent ) ) {
			return $atts;
		}

		$atts['el_width'] = $slug_to_percent[ $atts['el_width'] ];

		return $atts;
	}

	/**
	 * Migrate legacy add_button position dropdown to a toggle + separate btn_position attribute.
	 *
	 * Old: add_button stored the position ('top','bottom','left','right') or '' for no button.
	 * New: add_button is a toggle ('true'/''). Position lives in btn_position.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_add_button_dropdown_to_toggle( array $atts ): array {
		if ( isset( $atts['add_button'] ) && $this->is_cta_legacy_position( $atts['add_button'] ) ) {
			$atts['btn_position'] = $atts['add_button'];
			$atts['add_button']   = 'true';
		}

		return $atts;
	}

	/**
	 * Migrate legacy add_icon position dropdown to a toggle + separate i_position attribute.
	 *
	 * Old: add_icon stored the position ('top','bottom','left','right') or '' for no icon.
	 * New: add_icon is a toggle ('true'/''). Position lives in i_position.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function convert_cta_add_icon_dropdown_to_toggle( array $atts ): array {
		if ( isset( $atts['add_icon'] ) && $this->is_cta_legacy_position( $atts['add_icon'] ) ) {
			$atts['i_position'] = $atts['add_icon'];
			$atts['add_icon']   = 'true';
		}

		return $atts;
	}

	/**
	 * Populate `items_per_row` from the legacy `element_width` Bootstrap col-span.
	 *
	 * Reads element_width from $atts and writes items_per_row into $out.
	 * Pass the same array for both when source and destination are identical (edit form).
	 *
	 * @param array $out  Destination attribute array to receive items_per_row.
	 * @param array $atts Source attribute array containing element_width.
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_grid_element_width_to_items_per_row( array $out, array $atts ): array {
		if ( ! isset( $atts['element_width'] ) || '' === $atts['element_width'] ) {
			return $out;
		}
		$out['items_per_row'] = 12 / $atts['element_width'];

		return $out;
	}

	/**
	 * Append "px" to a legacy unit-less `gap` value in an attribute array.
	 *
	 * @param array $atts
	 * @return array
	 * @since 9.0
	 */
	public function normalize_grid_gap_to_px( array $atts ): array {
		if ( isset( $atts['gap'] ) && '' !== $atts['gap'] && is_numeric( $atts['gap'] ) ) {
			$atts['gap'] = esc_attr( $atts['gap'] ) . 'px';
		}

		return $atts;
	}

	/**
	 * Migrate a renamed attribute, copying the legacy value into the new key.
	 *
	 * Used when a param is renamed but keeps the same semantics/value, so elements
	 * saved under the old name keep rendering/editing correctly until re-saved.
	 *
	 * @param array  $out       Target attribute array to receive the new key.
	 * @param array  $raw       Source attribute array to read the legacy key from.
	 * @param string $old_key   Legacy attribute name.
	 * @param string $new_key   Current attribute name.
	 * @param bool   $unset_old Whether to remove the legacy key from $out after migration.
	 * @return array
	 * @since 9.0
	 */
	protected function migrate_renamed_attribute( array $out, array $raw, string $old_key, string $new_key, bool $unset_old = false ): array {
		if ( empty( $out[ $new_key ] ) && ! empty( $raw[ $old_key ] ) ) {
			$out[ $new_key ] = $raw[ $old_key ];
		}

		if ( $unset_old ) {
			unset( $out[ $old_key ] );
		}

		return $out;
	}
}
