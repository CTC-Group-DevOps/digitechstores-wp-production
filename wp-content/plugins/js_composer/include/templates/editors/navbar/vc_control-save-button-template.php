<?php
/**
 * Template for a save button in the navbar.
 *
 * @since 9.0
 * @var string $class
 * @var string $id
 * @var string $title
 * @var string $icon
 * @var string $text
 * @var string|null $data_change_status
 * @var string $tabindex
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<a href="javascript:;"
	id="<?php echo esc_attr( $id ); ?>"
	class="<?php echo esc_attr( $class ); ?>"
	<?php echo empty( $data_change_status ) ? '' : sprintf( ' data-change-status="%s"', esc_attr( $data_change_status ) ); ?>
	title="<?php echo esc_attr( $title ); ?>"
	role="button"
	tabindex="<?php echo esc_attr( $tabindex ); ?>"
>
		<div class="vc_hide-desktop">
			<?php vc_include_template( 'icons/' . $icon . '-ico.tpl.php' ); ?>
		</div>
		<p><?php echo esc_html( $text ); ?></p>
</a>
