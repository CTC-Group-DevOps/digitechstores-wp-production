<?php
/**
 * Tabs UI template.
 *
 * @var array $categories
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$with_tabs = count( $categories ) > 0;
if ( count( $categories ) > 0 ) :
	$first = true;
	?>
	<ul class="vc_general vc_ui-tabs-line" data-vc-ui-element="panel-tabs-controls" role="tablist" aria-label="<?php esc_html__( 'Template Tabs', 'js_composer' ); ?>">
		<?php
		foreach ( $categories as $key => $value ) :
			echo '<li class="vc_panel-tabs-control'
			. ( $first ? ' vc_active' : '' ) . '" role="presentation"><button data-vc-ui-element-target="[data-tab=' . esc_attr( trim( $key ) )
			. ']" class="vc_ui-tabs-line-trigger" data-vc-ui-element="panel-tab-control" role="tab">' . esc_html( $value ) . '</button>';
			echo '</li>';
			$first = false;
		endforeach;
		vc_include_template( 'editors/popups/partials/more-tabs-button.php' );
		?>
	</ul>
	<?php
endif;
