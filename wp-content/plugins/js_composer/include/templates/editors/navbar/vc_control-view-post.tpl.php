<?php
/**
 * Control View Post template.
 *
 * @var bool $is_mobile
 * @var integer $post_id
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<li class="vc_pull-right vc_hide-mobile <?php echo esc_attr( $is_mobile ? 'vc_hide-desktop' : '' ); ?>">
	<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"
		class="vc_icon-btn vc_back-button"
		title="<?php echo esc_attr( $title ); ?>"
		aria-label="<?php echo esc_attr( $title ); ?>">
			<?php vc_include_template( 'icons/close-ico.tpl.php' ); ?>
			<?php if ( $is_mobile ) : ?>
				<p aria-hidden="true"><?php esc_html_e( 'Close', 'js_composer' ); ?></p>
			<?php endif; ?>
	</a>
</li>
