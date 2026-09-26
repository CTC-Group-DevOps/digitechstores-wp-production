<?php
/**
 * Template for pagination start of Tabbed-Toggles-Accordions elements.
 *
 * @since 8.3
 * @var array  $classes
 * @var string $style Optional inline style (e.g. CSS variable for custom color).
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$style = (string) $style;
?>

<ul aria-label="Pagination" class="<?php echo esc_attr( $classes ); ?>"<?php echo '' !== $style ? ' style="' . esc_attr( $style ) . '"' : ''; ?>>
