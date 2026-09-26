<?php
/**
 * Add element tabs template.
 *
 * @var array $categories
 * @var object $box
 * @var bool|null $is_default_tab
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<ul class="vc_general vc_ui-tabs-line" data-vc-ui-element="panel-tabs-controls" role="tablist" aria-label="<?php esc_html__( 'Add Element Tabs', 'js_composer' ); ?>">
	<?php
	$tabs = $box->get_tabs( $categories );
	$tabs = apply_filters( 'vc_add_element_categories', $tabs );

	foreach ( $tabs as $key => $v ) :
		$classes = [ 'vc_edit-form-tab-control' ];
		if ( $v['active'] ) {
			$classes[] = 'vc_active';
		}
		?>
		<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-vc-ui-element="panel-add-element-tab" <?php echo isset( $is_default_tab ) ? 'data-tab-index="' . esc_attr( $key ) . '"' : ''; ?> role="presentation">
			<button class="vc_ui-tabs-line-trigger vc_add-element-filter-button"
					data-vc-ui-element="panel-tab-control"
					data-filter="<?php echo esc_attr( $v['filter'] ); ?>"
					role="tab"
			>
			<?php
			// @codingStandardsIgnoreLine
			print $v['name'];
			?>
			</button>
		</li>
		<?php
	endforeach;

	vc_include_template( 'editors/popups/partials/more-tabs-button.php' );
	?>
</ul>
