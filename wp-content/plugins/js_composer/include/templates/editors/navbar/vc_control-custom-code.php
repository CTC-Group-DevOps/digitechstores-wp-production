<?php
/**
 * Custom code control template for the navbar
 *
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<li class="vc_pull-right vc_hide-mobile vc_hide-desktop-more">
	<a href="javascript:;" id="vc_custom-code-button" class="vc_icon-btn vc_custom-code" title="<?php echo esc_attr( $title ); ?>" tabindex="8" role="button" aria-haspopup="dialog" aria-label="<?php echo esc_attr( $title ); ?>">
		<div class="vc_custom-code-icon">
			<div class="wpb-custom-code-icon-container">
				<?php vc_include_template( 'icons/custom-code-ico.tpl.php' ); ?>
				<span id="vc_custom-code-badge" class="vc_badge vc_badge-custom-code" style="display: none;" aria-hidden="true"></span>
			</div>
		</div>
		<p class="vc_hide-desktop" aria-hidden="true"><?php echo esc_html__( 'Custom Code', 'js_composer' ); ?></p>
	</a>
</li>
