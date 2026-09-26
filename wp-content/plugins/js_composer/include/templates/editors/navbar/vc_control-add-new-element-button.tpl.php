<?php
/**
 * Control add new element button template.
 *
 * @since 9.0
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<li class="vc_show-mobile">
	<a id="vc_add-new-element" class="vc_icon-btn vc_element-button" href="javascript:;" tabindex="2" data-model-id="vc_element" role="button" aria-haspopup="dialog" aria-label="<?php echo esc_attr( $title ); ?>" title="<?php echo esc_attr( $title ); ?>">
		<?php vc_include_template( 'icons/plus-ico.tpl.php' ); ?>
	</a>
</li>
