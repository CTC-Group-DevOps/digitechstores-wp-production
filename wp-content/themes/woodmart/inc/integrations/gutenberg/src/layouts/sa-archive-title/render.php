<?php

use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_shop_title' ) ) {
	function wd_gutenberg_shop_title( $block_attributes ) {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		$classes = '';
		$el_id   = wd_get_gutenberg_element_id( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$classes .= ' wd-align';
		}

		Main::setup_preview();

		$allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
		$html_tag     = isset( $block_attributes['htmlTag'] ) && in_array( $block_attributes['htmlTag'], $allowed_tags, true ) ? $block_attributes['htmlTag'] : 'span';

		ob_start();

		?>
		<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-woo-page-title<?php echo esc_attr( wd_get_gutenberg_element_classes( $block_attributes, $classes ) ); ?>">
			<<?php echo esc_attr( $html_tag ); ?>  class="entry-title title">
				<?php woocommerce_page_title(); ?>
			</<?php echo esc_attr( $html_tag ); ?>>
		</div>
		<?php
		Main::restore_preview();

		return ob_get_clean();
	}
}
