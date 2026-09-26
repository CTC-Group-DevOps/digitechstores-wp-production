<?php
/**
 * "Save Changes" button for admin settings tabs.
 *
 * @var array $attributes Extra HTML attributes for the button (e.g. LESS data attributes).
 */

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : [];
?>
<p class="submit">
	<button type="submit" name="submit_btn" id="submit_btn"
			class="button button-primary vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded vc_ui-button-fw"
			<?php
			foreach ( $attributes as $attr_name => $attr_value ) {
				echo esc_attr( $attr_name ) . '="' . esc_attr( $attr_value ) . '" ';
			}
			?>
	>
		<img class="vc_settings-save-spinner" src="<?php echo esc_url( vc_asset_url( 'vc/loaders/wizzy.svg' ) ); ?>" alt="" style="display: none;" aria-hidden="true" />
		<span class="vc_settings-save-label"><?php esc_html_e( 'Save Changes', 'js_composer' ); ?></span>
	</button>
</p>
