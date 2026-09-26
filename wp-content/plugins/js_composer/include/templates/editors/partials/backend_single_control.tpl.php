<?php
/**
 * Backend editor single control template.
 *
 * @since 9.0
 *
 * @var string|null $slug
 * @var string $classes
 * @var string $title
 * @var string|null $icon
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<a class="vc_control <?php echo esc_attr( $classes ); ?>" title="<?php echo esc_attr( $title ); ?>" data-vc-control="<?php echo esc_attr( $slug ); ?>" role="button" tabindex="0"><i class="vc-composer-icon <?php echo isset( $icon ) ? esc_attr( $icon ) : ''; ?>"></i></a>
