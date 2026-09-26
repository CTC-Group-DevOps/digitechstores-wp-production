<?php
/**
 * Autocomplete form field template.
 *
 * @var string $id              Field id attribute.
 * @var string $name            Field name attribute.
 * @var string $value           Field value (comma-separated for multiple).
 * @var string $tag             Shortcode tag.
 * @var array  $settings        Autocomplete settings.
 * @var bool   $is_inline       Whether to display inline.
 * @var bool   $is_multiple     Whether multiple selection is allowed.
 * @var string $placeholder     Placeholder text.
 * @var string $settings_json   JSON-encoded settings.
 * @var array  $parsed_values   Array of parsed value/label pairs.
 * @var string $classes         Additional CSS classes.
 *
 * @package WPBakeryPageBuilder
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div class="vc_autocomplete-field<?php echo $is_inline ? ' vc_autocomplete-inline' : ''; ?>">
	<select
		<?php echo $id ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
		class="vc_autocomplete wpb-form-select-search"
		<?php echo $is_multiple ? 'multiple="multiple"' : ''; ?>
		data-placeholder="<?php echo esc_attr( $placeholder ); ?>"
		<?php echo $settings_json ? 'data-settings="' . esc_attr( $settings_json ) . '"' : ''; ?>
	>
		<?php foreach ( $parsed_values as $parsed_value ) : ?>
			<option value="<?php echo esc_attr( $parsed_value['value'] ); ?>" selected="selected">
				<?php echo esc_html( $parsed_value['label'] ); ?>
			</option>
		<?php endforeach; ?>
	</select>

	<input
		name="<?php echo esc_attr( $name ); ?>"
		class="<?php echo esc_attr( $classes ); ?>"
		type="hidden"
		value="<?php echo esc_attr( $value ); ?>"
	/>
</div>
