<?php
/**
 * The template for displaying [vc_gallery] shortcode of 'Image gallery' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_gallery.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 *
 * @var array $atts
 * @var WPBakeryShortCode_Vc_gallery $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$thumbnail = '';
$title = $source = $type = $onclick = $custom_links = $custom_links_target = $img_size = $external_img_size = $images = $custom_srcs = $el_class = $el_id = $interval = $css = $css_animation = '';
$large_img_src = '';

$attributes = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $attributes );

/**
 * Shortcode attributes
 *
 * @var string $title
 * @var string $source
 * @var string $type
 * @var string $onclick
 * @var string $custom_links
 * @var string|null $custom_links_target
 * @var string $img_size
 * @var string $external_img_size
 * @var string $images
 * @var string $custom_srcs
 * @var string $el_class
 * @var string|null $el_id
 * @var string $interval
 * @var string $css
 * @var string $css_animation
 */

$default_src = vc_asset_url( 'vc/no_image.png' );

$gal_images = '';
$link_start = '';
$link_end = '';
$el_start = '';
$el_end = '';
$slides_wrap_start = '';
$slides_wrap_end = '';

$el_class = $this->getExtraClass( $el_class );
if ( 'nivo' === $type ) {
	$type = ' wpb_slider_nivo theme-default';
	wp_enqueue_script( 'nivo-slider' );
	wp_enqueue_style( 'nivo-slider-css' );
	wp_enqueue_style( 'nivo-slider-theme' );

	$slides_wrap_start = '<div class="nivoSlider">';
	$slides_wrap_end = '</div>';
} elseif ( 'flexslider' === $type || 'flexslider_fade' === $type || 'flexslider_slide' === $type || 'fading' === $type ) {
	$el_start = '<li>';
	$el_end = '</li>';
	$slides_wrap_start = '<ul class="slides">';
	$slides_wrap_end = '</ul>';
	wp_enqueue_style( 'wpb_flexslider' );
	wp_enqueue_script( 'wpb_flexslider' );
} elseif ( 'image_grid' === $type ) {
	wp_enqueue_script( 'vc_grid-js-imagesloaded' );
	wp_enqueue_script( 'isotope' );
	wp_enqueue_style( 'isotope-css' );

	$el_start = '<li class="isotope-item">';
	$el_end = '</li>';
	$slides_wrap_start = '<ul class="wpb_image_grid_ul">';
	$slides_wrap_end = '</ul>';
}

if ( 'link_image' === $onclick ) {
	wp_enqueue_script( 'lightbox2' );
	wp_enqueue_style( 'lightbox2' );
}

$flex_fx = '';
if ( 'flexslider' === $type || 'flexslider_fade' === $type || 'fading' === $type ) {
	$type = ' wpb_flexslider flexslider_fade flexslider';
	$flex_fx = ' data-flex_fx="fade"';
} elseif ( 'flexslider_slide' === $type ) {
	$type = ' wpb_flexslider flexslider_slide flexslider';
	$flex_fx = ' data-flex_fx="slide"';
} elseif ( 'image_grid' === $type ) {
	$type = ' wpb_image_grid';
}

if ( '' === $images ) {
	$images = '-1,-2,-3';
}

if ( 'custom_link' === $onclick ) {
	$custom_links = vc_value_from_safe( $custom_links );
	$custom_links = explode( ',', $custom_links );
}

$pretty_rel_random = 'lightbox[rel-' . get_the_ID() . '-' . wp_rand() . ']';
$image_data = json_decode( $images, true );

foreach ( $this->get_images_list( $images, $source, $custom_srcs ) as $i => $image ) {
	switch ( $source ) {
		case 'media_library':
			if ( $image > 0 ) {
				$img = wpb_getImageBySize( [
					'attach_id' => $image,
					'thumb_size' => $img_size,
				] );
				$thumbnail = $img['thumbnail'];
				$large_img_src = $img['p_img_large'][0];
			} else {
				$large_img_src = $default_src;
				$attributes = [
					'src' => esc_url( $large_img_src ),
				];
				$attributes = vc_add_lazy_loading_attribute( $attributes );
				$thumbnail = '<img ' . vc_stringify_attributes( $attributes ) . ' />';
			}
			break;

		case 'external_link':
			$dimensions = vc_extract_dimensions( $external_img_size );
			$hwstring = $dimensions ? image_hwstring( $dimensions[0], $dimensions[1] ) : '';

			$attributes = [
				'src' => esc_url( $image ),
			];
			$attributes = vc_add_lazy_loading_attribute( $attributes );

			$thumbnail = '<img ' . $hwstring . ' ' . vc_stringify_attributes( $attributes ) . ' />';
			$large_img_src = $image;
			break;
	}

	$link_html = '';

	switch ( $onclick ) {
		case 'img_link_large':
			$link_html = vc_get_template( 'partials/element-link.php', [
				'text' => $thumbnail,
				'wpb_link' => [
					'url' => $large_img_src,
					'target' => $custom_links_target,
				],
			] );
			break;

		case 'link_image':
			$link_html = vc_get_template( 'partials/element-link.php', [
				'text' => $thumbnail,
				'a_attrs' => [ 'data-lightbox' => $pretty_rel_random ],
				'wpb_link' => [
					'url' => $large_img_src,
					'target' => $custom_links_target,
				],
			] );
			break;

		case 'custom_link':
			if ( 'media_library' === $source && is_array( $image_data ) ) {
				$wpb_link = $image_data[ $image ] ?? [];
				$wpb_link['title'] = get_the_title( $image );

				$link_html = vc_get_template( 'partials/element-link.php', [
					'text' => $thumbnail,
					'wpb_link' => $wpb_link,
				] );
			} else {
				if ( ! empty( $custom_links[ $i ] ) ) {
					$link_html = vc_get_template( 'partials/element-link.php', [
						'text' => $thumbnail,
						'wpb_link' => [
							'url' => $custom_links[ $i ],
							'target' => $custom_links_target,
						],
					] );
				} else {
					$link_html = vc_get_template( 'partials/element-link.php', [
						'text' => $thumbnail,
					]);
				}
			}
			break;
		default:
			$link_html = $thumbnail;
	}

	$gal_images .= $el_start . $link_html . $el_end;
}

$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = 'wpb_gallery wpb_content_element vc_clearfix';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . ' ' . esc_attr( $element_class ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $settings['base'], $atts );
$wrapper_attributes = [];
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '';
$output .= '<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $wrapper_attributes ) . '>';
$output .= '<div class="wpb_wrapper">';
$output .= wpb_widget_title( [
	'title' => $title,
	'extraclass' => 'wpb_gallery_heading',
] );
$output .= '<div class="wpb_gallery_slides' . esc_attr( $type ) . '" data-interval="' . esc_attr( $interval ) . '"' . $flex_fx . '>' . $slides_wrap_start . $gal_images . $slides_wrap_end . '</div>';
$output .= '</div>';
$output .= '</div>';

return $output;
