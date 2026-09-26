<?php
/**
 * UI Post SEO template.
 *
 * @var array $header_tabs_template_variables
 * @var bool $can_unfiltered_html_cap
 * @var WP_Post | null $post
 * @var int $post_id
 * @var Vc_Post_Seo $vc_post_seo
 * @var string $permalink_structure
 * @var Vc_Post_Seo $box
 * @var string $id
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

vc_include_template( 'editors/popups/partials/modal-wrapper-start.php', [
	'id' => $id,
] );

vc_include_template('editors/popups/vc_ui-header.tpl.php', [
	'id' => $id,
	'title' => esc_html__( 'WPBakery SEO', 'js_composer' ),
	'controls' => [
		'panel-minimize' => [
			'title' => esc_html__( 'Minimize', 'js_composer' ),
		],
		'close' => [
			'title' => esc_html__( 'Close', 'js_composer' ),
		],
	],
	'header_css_class' => 'vc_ui-post-settings-header-container',
	'header_tabs_template' => 'editors/partials/add_element_tabs.tpl.php',
	'header_tabs_template_variables' => $header_tabs_template_variables,
	'is_default_tab' => true,
	'box' => $box,
]);
?>
<div class="vc_ui-panel-content-container">
	<div class="vc_ui-panel-content" data-vc-ui-element="panel-content">
		<form id="vc_setting-seo-form" action method="post">
			<div class="vc_panel-tabs">
				<?php
				foreach ( $header_tabs_template_variables['templates'] as $key => $template_name ) {
					$active_class = 0 === $key ? ' vc_active' : '';
					echo '<div id="vc_seo-tab-' . esc_attr( $key ) . '" class="vc_panel-tab' . esc_attr( $active_class ) . '" data-tab-index="' . esc_attr( $key ) . '">';
						$seo_settings = get_post_meta( $post_id, '_wpb_post_custom_seo_settings', true );
						vc_include_template(
							$template_name,
							[
								'is_active' => 0 === $key,
								'seo_settings' => json_decode( $seo_settings, true ),
								'post' => $post,
								'post_id' => $post_id,
								'vc_post_seo' => $vc_post_seo,
								'permalink_structure' => $permalink_structure,
							]
						);
					echo '</div>';
				}
				?>
			</div>
		</form>
	</div>
</div>
<!-- param window footer-->
<?php
vc_include_template('editors/popups/vc_ui-footer.tpl.php', [
	'controls' => [
		[
			'name' => 'close',
			'label' => esc_html__( 'Close', 'js_composer' ),
			'title' => esc_html( wpb_get_title_with_shortcut( 'Close' ) ),
		],
		[
			'name' => 'save',
			'label' => esc_html__( 'Save changes', 'js_composer' ),
			'title' => esc_html( wpb_get_title_with_shortcut( 'Save changes' ) ),
			'css_classes' => 'vc_ui-button-fw',
			'style' => 'action',
		],
	],
]);

vc_include_template( 'editors/popups/partials/modal-wrapper-end.php' );

