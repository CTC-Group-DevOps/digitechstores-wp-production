<?php
/**
 * Post settings control template for the navbar
 *
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<li class="vc_pull-right vc_hide-mobile vc_hide-desktop-more">
	<a id="vc_post-settings-button" href="javascript:;" tabindex="9" class="vc_icon-btn vc_post-settings" role="button" aria-haspopup="dialog" aria-label="<?php echo esc_attr( $title ); ?>" title="<?php echo esc_attr( $title ); ?>">
		<div class="vc_post-settings-icon">
			<?php vc_include_template( 'icons/settings-ico.tpl.php' ); ?>
		</div>
		<p class="vc_hide-desktop" aria-hidden="true"><?php echo esc_html__( 'Settings', 'js_composer' ); ?></p>
	</a>
</li>
