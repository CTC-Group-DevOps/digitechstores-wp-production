<?php
/**
 * The template for displaying all woocommerce shortcodes output elements.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_woocommerce.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 *
 * @var array $output
 * @var string $el_class
 * @var string $el_id
 */
?>


<div class="<?php echo esc_attr( $el_class ); ?>"
		<?php
		if ( $el_id ) {
			?>
			id="<?php echo esc_attr( $el_id ); ?>"
			<?php
		}
		?>
	>
	<?php
    // phpcs:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $output;
	?>
</div>
