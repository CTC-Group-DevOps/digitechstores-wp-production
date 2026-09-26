<?php
/**
 * Control redo button template.
 *
 * @since 9.0
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<li class="vc_hide-mobile vc_hide-desktop-more">
	<a id="vc_navbar-redo" class="vc_icon-btn vc_undo-redo vc_redo-button vc_hide-mobile" disabled tabindex="4" title="<?php echo esc_attr( $title ); ?>" role="button" aria-label="<?php echo esc_attr( $title ); ?>">
		<?php vc_include_template( 'icons/redo-ico.tpl.php' ); ?>
		<p class="vc_hide-desktop" aria-hidden="true">
			<?php echo esc_html__( 'Redo', 'js_composer' ); ?>
		</p>
	</a>
</li>
