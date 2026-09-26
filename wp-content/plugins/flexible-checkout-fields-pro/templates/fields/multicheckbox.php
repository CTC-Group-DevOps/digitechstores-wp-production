<?php
/**
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields/fields/multicheckbox.php
 *
 * @var string   $key               Field ID.
 * @var mixed[]  $args              Custom attributes for field.
 * @var mixed    $value             Field value.
 * @var string[] $custom_attributes .
 *
 * @package Flexible Checkout Fields PRO
 */

$value = ( is_array( $value ) ) ? $value : json_decode( $value, true );

?>
<fieldset class="form-row <?php echo esc_attr( $args['class'] ); ?> fcf-multi-checkbox"
	id="<?php echo esc_attr( $key ); ?>_field"
	data-priority="<?php echo esc_attr( $args['priority'] ); ?>"
	data-fcf-field="<?php echo esc_attr( $key ); ?>">
	<legend>
		<?php echo wp_kses_post( $args['label'] ); ?>
		<?php if ( $args['required'] ) : ?>
			<abbr class="required"
				title="<?php echo esc_attr( __( 'Required Field', 'flexible-checkout-fields-pro' ) ); ?>">*</abbr>
		<?php else : ?>
			<span class="optional">(<?php echo esc_html__( 'optional', 'woocommerce' ); ?>)</span>
		<?php endif; ?>
		<?php if ( $args['selected_min'] || $args['selected_max'] ) : ?>
			<br>
			<span>
					<?php if ( $args['selected_min'] ) : ?>
						<?php
						/* translators: %s: minimum value */
						echo esc_html( sprintf( __( 'Minimum: %s.', 'flexible-checkout-fields-pro' ), $args['selected_min'] ) );
						?>
					<?php endif; ?>
					<?php if ( $args['selected_max'] ) : ?>
						<?php
						/* translators: %s: maximum value */
						echo esc_html( sprintf( __( 'Limit: %s.', 'flexible-checkout-fields-pro' ), $args['selected_max'] ) );
						?>
					<?php endif; ?>
				</span>
		<?php endif; ?>
	</legend>
	<?php foreach ( $args['options'] as $option_data ) : ?>
		<label for="<?php echo esc_attr( $key ); ?>_<?php echo esc_attr( $option_data['key'] ); ?>">
			<input
				type="checkbox"
				class="input-radio"
				name="<?php echo esc_attr( $key ); ?>[]"
				id="<?php echo esc_attr( $key ); ?>_<?php echo esc_attr( $option_data['key'] ); ?>"
				value="<?php echo esc_attr( $option_data['key'] ); ?>"
				<?php echo esc_attr( in_array( $option_data['key'], ( $value ?: [] ) ) ? 'checked' : '' ); ?>
				data-fcf-field-input="<?php echo esc_attr( $key ); ?>" />
			<?php echo wp_kses_post( $option_data['value'] ); ?>
		</label>
	<?php endforeach; ?>
</fieldset>
