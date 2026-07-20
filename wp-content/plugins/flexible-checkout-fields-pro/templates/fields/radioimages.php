<?php
/**
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields/fields/radioimages.php
 *
 * @var string   $key               Field ID.
 * @var mixed[]  $args              Custom attributes for field.
 * @var mixed    $value             Field value.
 * @var string[] $custom_attributes .
 *
 * @package Flexible Checkout Fields PRO
 */

?>
<p class="form-row <?php echo esc_attr( $args['class'] ); ?> fcf-radio-images"
	id="<?php echo esc_attr( $key ); ?>_field"
	data-priority="<?php echo esc_attr( $args['priority'] ); ?>"
	data-fcf-field="<?php echo esc_attr( $key ); ?>">
	<label>
		<?php echo wp_kses_post( $args['label'] ); ?>
		<?php if ( $args['required'] ) : ?>
			<abbr class="required"
				title="<?php echo esc_attr( __( 'Required Field', 'flexible-checkout-fields-pro' ) ); ?>">*</abbr>
		<?php else : ?>
			<span class="optional">(<?php echo esc_html__( 'optional', 'woocommerce' ); ?>)</span>
		<?php endif; ?>
	</label>
	<span class="woocommerce-input-wrapper">
			<?php foreach ( $args['options'] as $option_data ) : ?>
				<input type="radio" class="input-radio fpf-input-field"
					value="<?php echo esc_html( $option_data['key'] ); ?>"
					name="<?php echo esc_attr( $key ); ?>"
					id="<?php echo esc_attr( $key . '_' . $option_data['key'] ); ?>"
					<?php echo ( $option_data['key'] === $value ) ? 'checked' : ''; ?>
					data-fcf-field-input="<?php echo esc_attr( $key ); ?>">
				<label for="<?php echo esc_attr( $key . '_' . $option_data['key'] ); ?>"
					title="<?php echo ( ! $args['preview_label_hide'] ) ? esc_attr( $option_data['value'] ) : ''; ?>"
					style="<?php echo ( $args['preview_width'] ) ? esc_attr( "width: {$args['preview_width']}px;" ) : ''; ?>">
					<?php echo wp_get_attachment_image( $option_data['image_id'] ); ?>
					<?php if ( ! $args['preview_label_hide'] ) : ?>
						<span><?php echo wp_kses_post( $option_data['value'] ); ?></span>
					<?php endif; ?>
				</label>
			<?php endforeach; ?>
		</span>
</p>
