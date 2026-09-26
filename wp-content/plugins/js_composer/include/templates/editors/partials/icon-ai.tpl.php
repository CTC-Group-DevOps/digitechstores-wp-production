<?php
/**
 * AI icon template.
 *
 * @var string $type
 * @var string $field_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$title = __( 'WPBakery AI Assistant', 'js_composer' );
?>
<button class="vc_ui-icon-ai"
	data-wpb-ai-element-type="<?php echo esc_attr( str_replace( ' ', '', $type ) ); ?>"
	type="button"
	<?php
	if ( is_string( $field_id ) ) {
		echo ' data-field-id="' . esc_attr( $field_id ) . '" ';
	}
	?>
	title="<?php echo esc_html( $title ); ?>"
	aria-label="<?php echo esc_html( $title ); ?>"
	role="button"
	tabindex="0"
	aria-haspopup="dialog"
>
	<svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="currentColor">
		<path fill="currentColor" d="M5.9985619 3C11.142858 3 12 6.1476669 12 8L11.996433 8.2069979L11.979631 8.4937973C11.853826 10.011008 11.093266 12.03775 8.2865458 12.744276C7.7295065 13.511618 6.8938098 14 5.9593368 14C5.0147443 14 4.1710792 13.500983 3.6152399 12.718781C0.64342582 11.924184 0.026770096 9.6232557 0.00087111775 8.102294L0 8C0 6.1476669 0.85426623 3 5.9985619 3ZM8.5714283 6L3.4285715 6C2.4817977 6 1.7142857 6.8954306 1.7142857 8C1.7142857 9.1045694 2.4817977 10 3.4285715 10L8.5714283 10C9.5182028 10 10.285714 9.1045694 10.285714 8C10.285714 6.8954306 9.5182028 6 8.5714283 6ZM4 7C4.5522847 7 5 7.4477153 5 8C5 8.5522852 4.5522847 9 4 9C3.4477153 9 3 8.5522852 3 8C3 7.4477153 3.4477153 7 4 7ZM8 7C8.5522852 7 9 7.4477153 9 8C9 8.5522852 8.5522852 9 8 9C7.4477153 9 7 8.5522852 7 8C7 7.4477153 7.4477153 7 8 7ZM2.5 0C3.3284271 0 4 0.67157286 4 1.5C4 2.3284271 3.3284271 3 2.5 3C1.6715729 3 1 2.3284271 1 1.5C1 0.67157286 1.6715729 0 2.5 0ZM9.5 0C10.328427 0 11 0.67157286 11 1.5C11 2.3284271 10.328427 3 9.5 3C8.6715727 3 8 2.3284271 8 1.5C8 0.67157286 8.6715727 0 9.5 0Z" fill-rule="evenodd"/>
	</svg>
	<span class="sr-only"><?php echo esc_html__( 'AI', 'js_composer' ); ?></span>
</button>
