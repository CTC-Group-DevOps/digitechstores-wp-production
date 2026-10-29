<?php use XTS\Modules\Layouts\Main;

if ( ! function_exists( 'wd_gutenberg_single_post_title' ) ) {
	function wd_gutenberg_single_post_title( $block_attributes ) {
		$el_id           = wd_get_gutenberg_element_id( $block_attributes );
		$wrapper_classes = wd_get_gutenberg_element_classes( $block_attributes );

		if ( ! empty( $block_attributes['textAlign'] ) || ! empty( $block_attributes['textAlignTablet'] ) || ! empty( $block_attributes['textAlignMobile'] ) ) {
			$wrapper_classes .= ' wd-align';
		}

		Main::setup_preview();
		$title = get_the_title();
		Main::restore_preview();

		if ( ! $title ) {
			return '';
		}

		$allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
		$html_tag     = isset( $block_attributes['htmlTag'] ) && in_array( $block_attributes['htmlTag'], $allowed_tags, true ) ? $block_attributes['htmlTag'] : 'h1';

		ob_start();
		woodmart_enqueue_inline_style( 'post-types-mod-predefined' );
		?>
		<div <?php echo $el_id ? 'id="' . esc_attr( $el_id ) . '" ' : ''; ?>class="wd-single-post-title<?php echo esc_attr( $wrapper_classes ); ?>">
			<<?php echo esc_attr( $html_tag ); ?> class="wd-post-title wd-entities-title entry-title title">
				<?php echo wp_kses_post( $title ); ?>
			</<?php echo esc_attr( $html_tag ); ?>>
		</div>
		<?php
		return ob_get_clean();
	}
}
