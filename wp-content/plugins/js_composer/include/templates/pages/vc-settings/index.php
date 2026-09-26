<?php
/**
 * Settings page wrapper template.
 *
 * @var Vc_Page $active_page
 * @var array $pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$is_welcome_page = 'vc-welcome' === $active_page->getSlug();
?>
<div class="wpb-settings-container">
	<div class="wpb-nav-container">
		<div class="wpb-nav-container-inner">
			<div class="wpb-logo-container">
				<a class="wpb-logo-link" href="https://wpbakery.com/?utm_source=wpdashboard&utm_medium=wpb-admin-menu&utm_campaign=info&utm_content=text" target="_blank" rel="noopener noreferrer">
					<img src="<?php echo esc_url( vc_asset_url( 'vc/logo/wpb-logo-white-full.svg' ) ); ?>" alt="<?php echo esc_attr__( 'WPBakery Page Builder', 'js_composer' ); ?>" class="wpb-logo">
				</a>
			</div>
			<?php
			vc_settings()->render_save_notice();
			vc_include_template( '/pages/partials/_settings_tabs.php',
					[
						'active_tab' => $active_page->getSlug(),
						'tabs' => $pages,
					] );
			?>
		</div>
	</div>
	<div class="wrap vc_settings<?php echo $is_welcome_page ? esc_attr( ' vc_settings--welcome' ) : ''; ?>" id="wpb-js-composer-settings">
		<?php if ( ! $is_welcome_page ) : ?>
			<h2 class="wpb-settings-header"><?php esc_html_e( 'WPBakery Page Builder Settings', 'js_composer' ); ?></h2>
		<?php endif; ?>
		<?php $active_page->render(); ?>
	</div>
</div>
