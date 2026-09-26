<?php
/**
 * Get more navbar menu template.
 *
 * @var array $controls
 * @var string $title
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$backend_editor_class = vc_is_frontend_editor() ? '' : 'vc_hide-desktop';
?>

<li class="vc_pull-right <?php echo esc_attr( $backend_editor_class ); ?> vc_show-mobile">
	<div class="vc_dropdown vc_dropdown-more" id="vc_more-options">
		<a href="javascript:;" class="vc_dropdown-toggle vc_icon-btn" title="<?php echo esc_attr( $title ); ?>" aria-haspopup="true" role="button" tabindex="12" aria-label="<?php echo esc_attr( $title ); ?>">
			<?php vc_include_template( 'icons/menu-ico.tpl.php' ); ?>
		</a>
		<ul class="vc_dropdown-list" role="menu" aria-label="<?php echo esc_attr__( 'More options', 'js_composer' ); ?>">
			<?php
			foreach ( $controls as $control ) :
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				print $control[1];
			endforeach;
			?>
		</ul>
	</div>
</li>
