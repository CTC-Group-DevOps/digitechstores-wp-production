<?php
/**
 * Control exit full screen button template.
 *
 * @since 9.0
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<li class="vc_pull-right">
	<a id="vc_windowed-button" class="vc_icon-btn vc_windowed-button" title="<?php echo esc_attr( $title ); ?>">
		<?php vc_include_template( 'icons/minimize-ico.tpl.php' ); ?>
	</a>
</li>
