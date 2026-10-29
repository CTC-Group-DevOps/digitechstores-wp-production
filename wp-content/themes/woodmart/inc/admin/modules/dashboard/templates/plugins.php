<?php
wp_enqueue_script( 'wd-setup-wizard', WOODMART_ASSETS . '/js/wizard.js', array(), WOODMART_VERSION, true );
?>

<div class="xts-box xts-theme-style">
	<div class="xts-box-header">
		<h3>
			<?php esc_html_e( 'Theme plugins', 'woodmart' ); ?>
		</h3>
	</div>

	<div class="xts-box-content">
		<?php
		get_template_part(
			'inc/admin/modules/setup-wizard/templates/plugins',
			'',
			array( 'show_plugins' => 'theme_plugin' )
		);
		?>
	</div>
	<?php if ( ! woodmart_get_opt( 'white_label' ) ) : ?>
		<div class="xts-box-footer">
			<p><?php esc_html_e( 'Plugins marked as "Required" are essential for the theme to function properly. Optional plugins provide additional features and can be deactivated or removed if not needed.', 'woodmart' ); ?></p>
		</div>
	<?php endif; ?>
</div>

