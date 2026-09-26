<?php
/**
 * Get navbar template button.
 *
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<li>
	<a href="javascript:;" class="vc_icon-btn vc_templates-button" id="vc_templates-editor-button" tabindex="3" role="button" aria-haspopup="dialog" aria-label="<?php echo esc_attr( $title ); ?>" title="<?php echo esc_attr( $title ); ?>">
		<?php vc_include_template( 'icons/layers-ico.tpl.php' ); ?>
	</a>
</li>
