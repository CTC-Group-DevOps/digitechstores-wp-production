<?php
/**
 * UI Header template.
 *
 * @var string $header_css_class
 * @var string|null $title
 * @var array $controls
 * @var string $search_template
 * @var string $header_tabs_template
 * @var array|null $header_tabs_template_variables
 * @var bool|null $stacked_bottom
 * @var bool $is_ai_token_usage
 * @var string|null $id
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<!-- param window header-->
<div
	class="<?php echo esc_attr( $header_css_class ); ?> vc_ui-panel-header-container <?php echo ( isset( $stacked_bottom ) && $stacked_bottom ) || ! isset( $stacked_bottom ) ? 'vc_ui-panel-header-o-stacked-bottom' : ''; ?>"
	data-vc-ui-element="panel-heading">
	<div class="vc_ui-panel-header">
		<div class="vc_ui-panel-header-header vc_ui-grid-gap" data-vc-panel-container=".vc_ui-panel-header-container">
			<h3
				<?php echo isset( $id ) ? sprintf( 'id="%s-title"', esc_attr( $id ) ) : ''; ?>
				class="vc_ui-panel-header-heading"
				data-vc-ui-element="panel-title">
				<i class="vc-composer-icon vc-c-param-group-dragndrop"></i>
				<?php
				echo isset( $title ) ? esc_html( $title ) : '';
				if ( ! empty( $is_ai_token_usage ) ) :
					vc_include_template( 'editors/popups/ai/token-usage.tpl.php' );
				endif
				?>
			</h3>
			<?php
			if ( ! empty( $search_template ) ) :
				vc_include_template( $search_template );
			endif
			?>
			<div class="vc_ui-panel-header-controls">
				<?php
				foreach ( $controls as $key => $control ) :
					if ( is_array( $control ) && isset( $control['template'] ) ) :
						vc_include_template( $control['template'], isset( $control['variables'] ) ? $control['variables'] : [] );
					elseif ( is_array( $control ) ) :
						vc_include_template(
								'editors/popups/partials/popup-control-button.php',
								[
									'control_slug' => $key,
									'title' => isset( $control['title'] ) ? $control['title'] : ucfirst( $key ),
								]
						);
					else :
						vc_include_template(
								'editors/popups/partials/popup-control-button.php',
								[
									'control_slug' => $control,
									'title' => ucfirst( $control ),
								]
						);
						?>
					<?php endif ?>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="vc_ui-panel-header-content" data-vc-ui-element="panel-header-content">
			<?php
			if ( ! empty( $header_tabs_template ) ) :
				$payload = isset( $header_tabs_template_variables ) && is_array( $header_tabs_template_variables ) ? $header_tabs_template_variables : [];
				$payload = isset( $box ) ? array_merge( $payload, [ 'box' => $box ] ) : $payload;

				vc_include_template( $header_tabs_template, $payload );
				endif
			?>
		</div>
	</div>
</div>
