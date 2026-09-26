<?php
/**
 * Control seo buttons template.
 *
 * @since 9.0
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<li class="vc_pull-right vc_hide-mobile vc_hide-desktop-more">
	<a href="javascript:;" class="vc_icon-btn vc_seo-button" id="vc_seo-button" tabindex="6" title="<?php echo esc_attr( $title ); ?>" role="button" aria-haspopup="dialog" aria-label="<?php echo esc_attr( $title ); ?>">
		<div class="wpb-seo-wrapper">
			<?php vc_include_template( 'icons/chart-ico.tpl.php' ); ?>
		</div>
		<p class="vc_hide-desktop" aria-hidden="true"><?php echo esc_html__( 'SEO', 'js_composer' ); ?></p>
	</a>
</li>
