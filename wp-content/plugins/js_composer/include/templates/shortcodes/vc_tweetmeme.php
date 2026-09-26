<?php
/**
 * The template for displaying [vc_tweetmeme] shortcode output of 'X button' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_tweetmeme.php.
 *
 * @note For a frontend editor this element uses $this::contentInline() method to output template.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 * @see WPBakeryShortCode_Vc_TweetMeMe::contentInline()
 *
 * @var array $atts
 * @var WPBakeryShortCode_Vc_TweetMeMe $this
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// backward compatibility.
$legacy_map = [
	'mention_no_default'    => [
		'on'          => 'add',
		'value_field' => 'mention_custom_tweet_text',
	],
	'hashtag_no_default'    => [
		'on'          => 'add',
		'value_field' => 'hashtag_custom_tweet_text',
	],
	'hashtag_no_url'        => [
		'on'          => 'add',
		'value_field' => 'hashtag_custom_tweet_url',
	],
	'share_use_page_url'    => [
		'on'          => 'use_custom',
		'value_field' => 'share_use_custom_url',
	],
	'share_text_page_title' => [
		'on'          => 'use_custom',
		'value_field' => 'share_text_custom_text',
	],
];
foreach ( $legacy_map as $legacy_param => $config ) {
	if ( isset( $atts[ $legacy_param ] ) && '' === $atts[ $legacy_param ] && ! empty( $atts[ $config['value_field'] ] ) ) {
		$atts[ $legacy_param ] = $config['on'];
	}
}
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

$tweet_btn_text = '';
$type = $atts['type'];

switch ( $type ) {
	case 'follow':
		$tweet_btn_text = esc_html__( 'Follow', 'js_composer' );
		break;

	case 'mention':
		$tweet_btn_text = esc_html__( 'Tweet to', 'js_composer' );
		break;

	case 'share':
	case 'hashtag':
		$tweet_btn_text = esc_html__( 'Tweet', 'js_composer' );
		break;
	default:
		$type = 'share';
		$tweet_btn_text = esc_html__( 'Tweet', 'js_composer' );
		break;
}
$data = [];
$classes = [];
$settings = $this->getSettings();
$element_class = empty( $settings['element_default_class'] ) ? '' : $settings['element_default_class'];
$class_to_filter = 'vc_tweetmeme-element' . vc_shortcode_custom_css_class( $atts['css'], ' ' ) . $this->getCSSAnimation( $atts['css_animation'] ) . ' ' . esc_attr( $element_class ) . $this->getExtraClass( $atts['el_class'] );
$el_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->getShortcode(), $atts );
if ( ! empty( $atts['large_button'] ) ) {
	$data['data-size'] = 'large';
}

$url = 'https://twitter.com/';
if ( 'share' === $type ) {
	$url = 'https://twitter.com/share';
	$classes[] = 'twitter-share-button';

	if ( 'use_custom' === $atts['share_use_page_url'] && ! empty( $atts['share_use_custom_url'] ) ) {
		$share_link = vc_build_link( $atts['share_use_custom_url'] );
		$data['data-url'] = empty( $share_link['url'] ) ? $atts['share_use_custom_url'] : $share_link['url'];
	}
	if ( 'use_custom' === $atts['share_text_page_title'] && ! empty( $atts['share_text_custom_text'] ) ) {
		$data['data-text'] = $atts['share_text_custom_text'];
	}
	if ( ! empty( $atts['share_via'] ) ) {
		$data['data-via'] = $atts['share_via'];
	}
	if ( ! empty( $atts['share_recommend'] ) ) {
		$data['data-related'] = $atts['share_recommend'];
	}
	if ( ! empty( $atts['share_hashtag'] ) ) {
		$data['data-hashtags'] = $atts['share_hashtag'];
	}
} elseif ( 'follow' === $type ) {
	$url = 'https://twitter.com/';
	$classes[] = 'twitter-follow-button';
	if ( ! empty( $atts['follow_user'] ) ) {
		$url .= esc_attr( $atts['follow_user'] );
		$tweet_btn_text .= ' @' . esc_attr( $atts['follow_user'] );
	}
	if ( 'yes' !== $atts['follow_show_username'] ) {
		$data['data-show-screen-name'] = 'false';
	}
	$data['data-show-count'] = ( ! ! $atts['show_followers_count'] ) ? 'true' : 'false'; // phpcs:ignore Universal.CodeAnalysis.NoDoubleNegative.FoundDouble

} elseif ( 'hashtag' === $type ) {
	$url = 'https://twitter.com/intent/tweet?';
	$classes[] = 'twitter-hashtag-button';
	$url_atts = [];
	if ( ! empty( $atts['hashtag_hash'] ) ) {
		$url_atts[] = 'button_hashtag=' . esc_attr( $atts['hashtag_hash'] );
		$tweet_btn_text .= ' #' . esc_attr( $atts['hashtag_hash'] );
	}
	if ( 'add' === $atts['hashtag_no_default'] && ! empty( $atts['hashtag_custom_tweet_text'] ) ) {
		$url_atts[] = 'text=' . esc_attr( $atts['hashtag_custom_tweet_text'] );
	}
	$url .= implode( '&', $url_atts );

	$rel = [];
	if ( ! empty( $atts['hashtag_recommend_1'] ) ) {
		$rel[] = $atts['hashtag_recommend_1'];
	}
	if ( ! empty( $atts['hashtag_recommend_2'] ) ) {
		$rel[] = $atts['hashtag_recommend_2'];
	}
	if ( ! empty( $rel ) ) {
		$data['data-related'] = implode( ',', $rel );
	}
	if ( 'add' === $atts['hashtag_no_url'] && ! empty( $atts['hashtag_custom_tweet_url'] ) ) {
		$hashtag_link = vc_build_link( $atts['hashtag_custom_tweet_url'] );
		$data['data-url'] = empty( $hashtag_link['url'] ) ? $atts['hashtag_custom_tweet_url'] : $hashtag_link['url'];
	}
} elseif ( 'mention' === $type ) {
	$url = 'https://twitter.com/intent/tweet?';
	$classes[] = 'twitter-mention-button';
	$url_atts = [];
	if ( ! empty( $atts['mention_tweet_to'] ) ) {
		$url_atts[] = 'screen_name=' . esc_attr( $atts['mention_tweet_to'] );
		$tweet_btn_text .= ' @' . esc_attr( $atts['mention_tweet_to'] );
	}
	if ( 'add' === $atts['mention_no_default'] && ! empty( $atts['mention_custom_tweet_text'] ) ) {
		$url_atts[] = 'text=' . esc_attr( $atts['mention_custom_tweet_text'] );
	}
	$url .= implode( '&', $url_atts );

	$rel = [];
	if ( ! empty( $atts['mention_recommend_1'] ) ) {
		$rel[] = $atts['mention_recommend_1'];
	}
	if ( ! empty( $atts['mention_recommend_2'] ) ) {
		$rel[] = $atts['mention_recommend_2'];
	}
	if ( ! empty( $rel ) ) {
		$data['data-related'] = implode( ',', $rel );
	}
}

if ( ! empty( $atts['lang'] ) ) {
	$data['data-lang'] = $atts['lang'];
}
$data_imploded = [];
foreach ( $data as $k => $v ) {
	$data_imploded[] = $k . '="' . esc_attr( $v ) . '"';
}
$wrapper_attributes = [];
if ( ! empty( $atts['el_id'] ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $atts['el_id'] ) . '"';
}
$wrapper = '<div ' . implode( ' ', $wrapper_attributes ) . ' class="' . esc_attr( $el_class ) . '">';
$template = '<a href="' . esc_url( $url ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '" ' . implode( ' ', $data_imploded ) . '>' . $tweet_btn_text . '</a>';
$custom_tag = 'script';
$template .= '<' . $custom_tag . '>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</' . $custom_tag . '>';
$wrapper .= $template;
$wrapper .= '</div>';

return $wrapper;
