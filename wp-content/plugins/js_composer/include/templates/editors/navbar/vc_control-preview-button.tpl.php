<?php
/**
 * Control preview button template.
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
	<a href="<?php echo esc_url( $link ); ?>" title="<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer">
		<?php vc_include_template( 'icons/view-ico.tpl.php' ); ?>
		<p class="wpb-desktop-preview-text"><?php echo esc_html( $title ); ?></p>
	</a>
</li>
