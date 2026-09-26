<?php
/**
 * Control brand button template.
 *
 * @since 9.0
 * @var string $title
 * @var string $link
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<a id="vc_logo" class="vc_icon-btn vc_navbar-brand" tabindex="1" aria-label="<?php echo esc_attr( $title ); ?>" title="<?php echo esc_attr( $title ); ?>" href="<?php echo esc_url( $link ); ?>" target="_blank">
	<?php vc_include_template( 'icons/wpbakery-ico.tpl.php' ); ?>
</a>
