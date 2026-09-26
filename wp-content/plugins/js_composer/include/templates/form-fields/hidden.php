<?php
/**
 * Hidden form field template.
 *
 * @var string $name
 * @var string $value
 * @var string $classes
 * @var bool $is_value_escape
 * @var string|null $data_attr_string
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<input type="hidden"
	name="<?php echo esc_attr( $name ); ?>"
	value="<?php echo $is_value_escape ? esc_attr( $value ) : $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
	class="<?php echo esc_attr( $classes ); ?>"
	<?php
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped during string build.
	echo $data_attr_string ?? '';
	?>
>
