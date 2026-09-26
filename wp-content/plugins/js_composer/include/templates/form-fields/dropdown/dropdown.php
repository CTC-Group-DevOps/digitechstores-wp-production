<?php
/**
 * Dropdown form field template.
 *
 * @var string $id
 * @var string $classes
 * @var string $name
 * @var bool $has_search
 * @var array $options
 * @var string|null $data_attr_string
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<select
		class="<?php echo ( $has_search ? 'wpb-form-select-search ' : 'wpb-form-select ' ) . esc_attr( $classes ); ?>"
		<?php
		if ( '' !== $id ) {
			echo ' id="' . esc_attr( $id ) . '"';
		}
		if ( '' !== $name ) {
			echo ' name="' . esc_attr( $name ) . '"';
		}
		?>
		<?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped during string build.
		echo $data_attr_string ?? '';
		?>
>
	<?php
	foreach ( $options as $option_index => $option_data ) {
		if ( isset( $option_data['value'] ) && is_array( $option_data['value'] ) ) {
			?>
			<optgroup
				<?php
				if ( isset( $option_data['label'] ) ) {
					echo ' label="' . esc_attr( $option_data['label'] ) . '"';
				}
				?>
			>
				<?php
				foreach ( $option_data['value'] as $sub_option_index => $sub_option_data ) {
					vc_include_template(
						'form-fields/dropdown/option.php',
						[
							'option_data' => $sub_option_data,
						]
					);
				}
				?>
			</optgroup>
			<?php
		} else {
			vc_include_template(
				'form-fields/dropdown/option.php',
				[
					'option_data' => $option_data,
				]
			);
		}
	}
	?>
</select>
