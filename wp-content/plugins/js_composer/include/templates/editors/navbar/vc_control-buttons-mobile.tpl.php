<?php
/**
 * Control button save post in backend editor for mobile template.
 *
 * @var string $save_text
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<li class="vc_pull-right vc_save-backend">
	<a class="vc_control-preview vc_icon-btn" title="<?php echo esc_attr( wpb_get_title_with_shortcut( 'Preview' ) ); ?>">
		<div class="vc_hide-desktop">
			<?php vc_include_template( 'icons/view-ico.tpl.php' ); ?>
		</div>
		<p><?php esc_html_e( 'Preview', 'js_composer' ); ?></p>
	</a>
	<a class="vc_icon-btn vc_control-save" id="wpb-save-post" title="<?php echo esc_attr( wpb_get_title_with_shortcut( $save_text ) ); ?>">
		<div class="vc_hide-desktop">
			<?php vc_include_template( 'icons/check-ico.tpl.php' ); ?>
		</div>
		<p><?php echo esc_html( $save_text ); ?></p>
	</a>
</li>
