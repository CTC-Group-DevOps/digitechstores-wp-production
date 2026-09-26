<?php
/**
 * Close div template.
 *
 * @param string $classes
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div
		<?php
		if ( ! empty( $classes ) ) {
			echo 'class="' . esc_attr( $classes ) . '"';
		}
		?>
>
