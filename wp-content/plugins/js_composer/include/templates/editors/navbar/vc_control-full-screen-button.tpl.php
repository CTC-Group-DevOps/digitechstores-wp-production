<?php
/**
 * Control full screen button template.
 *
 * @since 9.0
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<li class="vc_pull-right vc_hide-mobile">
	<a id="vc_fullscreen-button" class="vc_icon-btn vc_fullscreen-button" title="<?php echo esc_attr( $title ); ?>" role="button" tabindex="7">
		<?php vc_include_template( 'icons/maximize-ico.tpl.php' ); ?>
	</a>
</li>
