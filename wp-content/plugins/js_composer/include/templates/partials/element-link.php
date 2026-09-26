<?php
/**
 * Link for elements templates
 *
 * @var array $a_attrs
 * @var string $class
 * @var array $wpb_link
 * @var string $text
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( empty( $wpb_link['url'] ) ) {
	return $text;
}
?>

<a
	href="<?php echo esc_url( $wpb_link['url'] ); ?>"
	<?php
	if ( ! empty( $class ) ) :
		?>
		class="<?php echo esc_attr( $class ); ?>"
		<?php
	endif;
	if ( ! empty( $a_attrs ) ) :
		echo vc_stringify_attributes( $a_attrs ); // phpcs:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
	endif;
	if ( ! empty( $wpb_link['target'] ) && '_self' !== $wpb_link['target'] ) :
		?>
		target="<?php echo esc_attr( $wpb_link['target'] ); ?>"
		<?php
	endif;
	if ( ! empty( $wpb_link['rel'] ) ) :
		?>
		rel="<?php echo esc_attr( $wpb_link['rel'] ); ?>"
		<?php
	endif;
	if ( ! empty( $wpb_link['title'] ) ) :
		?>
		title="<?php echo esc_attr( $wpb_link['title'] ); ?>"
	<?php endif; ?>
>
	<?php echo wp_kses_post( $text ); ?>
</a>
