<?php
/**
 * Template for button_group form component.
 *
 * Reusable button group that can be used both as a param and as a standalone form component.
 *
 * @var string $type Field type.
 * @var string $name Input name attribute.
 * @var string $id Base ID for generating radio input IDs.
 * @var string $class Additional CSS class for the container.
 * @var string $input_classes Additional CSS classes for each radio input.
 * @var array  $options Processed options with type, label, title, icon_class.
 * @var string $heading Field heading for accessibility.
 * @var string $current_value Currently selected value.
 * @var string $icon_size Custom icon size (e.g., '20px', '1.5em').
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="wpb_button_group-container <?php echo esc_attr( $class ); ?>"
	role="radiogroup"
	aria-label="<?php echo esc_attr( $heading ); ?>">
	<div class="wpb_button_group-buttons">
		<?php
		$index = 0;
		$options = vc_button_group_process_options( $options );
		foreach ( $options as $option_value => $option ) :
			$button_id = $id . '_' . $index;
			$is_checked = ( $current_value === (string) $option_value );
			$button_type_class = 'icon' === $option['type'] || 'image' === $option['type'] ? 'wpb_button_group-button--icon' : 'wpb_button_group-button--text';
			$index++;
			?>
			<div class="wpb_button_group-button-wrapper">
				<input type="radio"
					class="wpb_button_group-input <?php echo esc_attr( $input_classes ); ?>"
					id="<?php echo esc_attr( $button_id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					value="<?php echo esc_attr( $option_value ); ?>"
					<?php checked( $is_checked ); ?> />
				<?php
				$icon_style = ! empty( $icon_size ) ? 'width:' . esc_attr( $icon_size ) . ';height:' . esc_attr( $icon_size ) . ';font-size:' . esc_attr( $icon_size ) . ';' : '';
				$use_aria_label = 'image' === $option['type'] || 'icon' === $option['type'];
				?>
				<label class="wpb_button_group-button <?php echo esc_attr( $button_type_class ); ?>"
					for="<?php echo esc_attr( $button_id ); ?>"
					title="<?php echo esc_attr( $option['title'] ); ?>"
					<?php echo $use_aria_label ? 'aria-label="' . esc_attr( $option['title'] ) . '"' : ''; ?>>
					<?php if ( 'image' === $option['type'] ) : ?>
						<img class="wpb_button_group-icon-img" src="<?php echo esc_url( $option['label'] ); ?>" alt="<?php echo esc_attr( $option['title'] ); ?>"<?php echo $icon_style ? ' style="' . esc_attr( $icon_style ) . '"' : ''; ?> />
					<?php elseif ( 'icon' === $option['type'] ) : ?>
						<span class="wpb_button_group-icon <?php echo esc_attr( $option['icon_class'] ); ?>" aria-hidden="true"<?php echo $icon_style ? ' style="' . esc_attr( $icon_style ) . '"' : ''; ?>></span>
					<?php else : ?>
						<span class="wpb_button_group-text"><?php echo esc_html( $option['label'] ); ?></span>
					<?php endif; ?>
				</label>
			</div>
		<?php endforeach; ?>
	</div>
</div>
