<?php
/**
 * Settings tabs template.
 *
 * @var string $active_tab
 * @var array $tabs
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<!-- Desktop Navigation -->
<nav class="wpb-nav wpb-nav--desktop" role="navigation" aria-label="<?php esc_attr_e( 'Settings pages', 'js_composer' ); ?>">
	<?php foreach ( $tabs as $slug => $title ) : ?>
		<?php $url = 'admin.php?page=' . rawurlencode( $slug ); ?>
		<a href="<?php echo esc_attr( is_network_admin() ? network_admin_url( $url ) : admin_url( $url ) ); ?>"
				class="wpb-nav-item<?php echo $active_tab === $slug ? esc_attr( ' wpb-nav-item--active' ) : ''; ?>"
				role="tab"
				title="<?php echo esc_attr( $title ); ?>"
				aria-selected="<?php echo $active_tab === $slug ? 'true' : 'false'; ?>">
			<?php
			$icon_template = VcSharedLibrary::getSettingsNavIcon( $slug );
			if ( $icon_template ) {
				echo '<span class="wpb-nav-item__icon" role="img" aria-hidden="true">';
				vc_include_template( $icon_template );
				echo '</span>';
			}
			?>
			<span class="wpb-nav-item__title"><?php echo esc_html( $title ); ?></span>
		</a>
	<?php endforeach ?>
</nav>

<!-- Mobile Navigation Dropdown -->
<div class="wpb-nav wpb-nav--mobile">
	<select class="wpb-form-select wpb-nav-select" aria-label="<?php esc_attr_e( 'Settings pages', 'js_composer' ); ?>" name="wpb_settings_nav">
		<?php foreach ( $tabs as $slug => $title ) : ?>
			<?php $url = is_network_admin() ? network_admin_url( 'admin.php?page=' . rawurlencode( $slug ) ) : admin_url( 'admin.php?page=' . rawurlencode( $slug ) ); ?>
			<option value="<?php echo esc_attr( $url ); ?>" <?php selected( $active_tab, $slug ); ?>>
				<?php echo esc_html( $title ); ?>
			</option>
		<?php endforeach ?>
	</select>
</div>

