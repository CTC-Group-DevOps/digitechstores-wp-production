<?php
/**
 * Control backend editor button template.
 *
 * @since 9.0
 * @var string $title
 * @var string $link
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<li class="vc_dropdown-list-item">
	<a href="<?php echo esc_url( $link ) . '&wpb-backend-editor'; ?>" title="<?php echo esc_attr( $title ); ?>">
			<?php vc_include_template( 'icons/layout-ico.tpl.php' ); ?>
		<p class="wpb-desktop-preview-text">
			<?php echo esc_html( $title ); ?>
		</p>
	</a>
</li>
