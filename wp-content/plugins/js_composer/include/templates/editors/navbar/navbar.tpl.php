<?php
/**
 * Navbar template.
 *
 * @var Vc_Navbar $nav_bar
 * @var array $controls
 * @var string $css_class
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$class = ! empty( $css_class ) ? $css_class : 'vc_navbar';
if ( vc_modules_manager()->is_module_on( 'vc-post-custom-layout' ) ) {
	$custom_layout = vc_modules_manager()->get_module( 'vc-post-custom-layout' );

	if ( $custom_layout->get_custom_layout_name() ) {
		$template_class = ' vc_post-custom-layout-selected';
	} else {
		$template_class = '';
	}
} else {
	$template_class = ' vc_post-custom-layout-selected';
}
$class .= $template_class;
$class .= ! empty( $post ) && ! empty( $post->post_content ) ? ' vc_not-empty' : '';
?>
<div class="<?php echo esc_attr( $class ); ?>"
	role="navigation"
	id="vc_navbar">
	<div class="vc_navbar-header">
		<?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		print $nav_bar->getLogo();
		?>
	</div>
	<ul class="vc_navbar-nav">
		<?php
		foreach ( $controls as $control ) :
			if ( is_string( $control[1] ) ) {
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				print $control[1];
			}
			if ( is_array( $control[1] ) ) {
				vc_include_template(
						'editors/navbar/vc_control-get-more-buttons.tpl.php',
						[
							'title' => __( 'More', 'js_composer' ),
							'controls' => $control[1],
						]
				);
			}
		endforeach;
		?>
	</ul>
</div>
