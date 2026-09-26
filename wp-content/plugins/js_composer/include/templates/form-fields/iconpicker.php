<?php
/**
 * Iconpicker form field template.
 *
 * @var string $classes
 * @var string $name
 * @var string $value
 * @var string $id
 * @var array $icon_lib_list
 * @var array $data_attributes
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="vc-iconpicker-wrapper">
	<select id="<?php echo esc_attr( $id ); ?>" class="vc-iconpicker">
		<?php
		foreach ( $icon_lib_list as $group => $icons ) {
			if ( is_array( $icons ) && is_array( current( $icons ) ) ) {
				vc_include_template( 'params/iconpicker/icon_group.php',
					[
						'icons' => $icons,
						'group' => $group,
						'value' => $value,
					]
				);
			} else {
				$class_key = empty( $icons ) ? '' : key( $icons );
				vc_include_template( 'params/iconpicker/single_icon.php',
						[
							'class_key' => $class_key,
							'selected' => null !== $value && 0 === strcmp( $class_key, $value ) ? 'selected' : '',
							'icon' => current( $icons ),
						]
				);
			}
		}
		?>
	</select>
</div>

<input type="hidden"
	name="<?php echo esc_attr( $name ); ?>"
	value="<?php echo esc_attr( $value ); ?>"
	class="<?php echo esc_attr( $classes ); ?>"
	<?php
	foreach ( $data_attributes as $data_attr_name => $data_attr_value ) {
		echo ' data-' . esc_attr( $data_attr_name ) . '="' . esc_attr( $data_attr_value ) . '"';
	}
	?>
>
