<?php
/**
 * Link form field template.
 *
 * @var string $value
 * @var array $link
 * @var string $name
 * @var string $type
 * @var array $settings
 * @var string $id
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="wpb_link-field"
	data-url="<?php echo esc_attr( $link['url'] ); ?>"
	data-title="<?php echo esc_attr( $link['title'] ); ?>"
	data-target="<?php echo esc_attr( $link['target'] ); ?>"
	data-rel="<?php echo esc_attr( $link['rel'] ); ?>"
		<?php if ( 'href' === $type ) : ?>
			data-hide-title="true"
		<?php endif; ?>
>
	<input
			type="hidden"
			name="<?php echo esc_attr( $name ); ?>"
			class="wpb_vc_param_value <?php echo esc_attr( $name . ' ' . $type . '_field' ); ?>"
			value="<?php echo is_string( $value ) ? esc_attr( $value ) : ''; ?>"
	>

	<div class="wpb_link-input-wrapper">
		<div class="wpb_link-search-wrap">
			<input type="text"
					id="<?php echo esc_attr( $id ); ?>"
					class="wpb-form-input wpb_link-select"
					placeholder="<?php echo esc_attr__( 'Search or type URL', 'js_composer' ); ?>"
					value="<?php echo esc_attr( $link['url'] ); ?>"
			>
			<select class="wpb-input-dropdown-select" style="display: none;" tabindex="-1"></select>
		</div>
		<button type="button"
				class="wpb_link-edit-btn"
				aria-label="<?php echo esc_attr__( 'Insert or edit link', 'js_composer' ); ?>"
				title="<?php echo esc_attr__( 'Insert or edit link', 'js_composer' ); ?>">
			<i class="vc-composer-icon vc-c-linked"></i>
		</button>
	</div>

	<?php
	vc_include_template( 'form-fields/link/modal.php', [
		'link'     => $link,
		'name'     => $name,
		'settings' => $settings,
		'id'       => $id,
	] );
	?>
</div>
