<?php
/**
 * Control frontend editor button template.
 *
 * @since 9.0
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<li class="vc_pull-right" style="display: none;">
	<a href="<?php echo esc_url( vc_frontend_editor()->getInlineUrl() ); ?>" class="vc_btn vc_btn-primary vc_btn-sm vc_navbar-btn" id="wpb-edit-inline">
		<?php echo esc_html( $title ); ?>
	</a>
</li>
