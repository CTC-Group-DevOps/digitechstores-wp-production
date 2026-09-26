<?php
/**
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields/fields/inspireradio.php
 *
 * @var string   $key               Field ID.
 * @var mixed[]  $args              Custom attributes for field.
 * @var mixed    $value             Field value.
 * @var string[] $custom_attributes .
 *
 * @package Flexible Checkout Fields PRO
 */

?>
<fieldset class="form-row <?php echo esc_attr( $args['class'] ); ?>"
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
	</legend>
	<?php foreach ( $args['options'] as $option_data ) : ?>
		<label for="<?php echo esc_attr( $key ); ?>_<?php echo esc_attr( $option_data['key'] ); ?>">
			<input
				type="radio"
				class="input-radio"
				name="<?php echo esc_attr( $key ); ?>"
				id="<?php echo esc_attr( $key ); ?>_<?php echo esc_attr( $option_data['key'] ); ?>"
				value="<?php echo esc_attr( $option_data['key'] ); ?>"
				<?php echo ( $value == $option_data['key'] ) ? 'checked' : ''; ?>
				data-fcf-field-input="<?php echo esc_attr( $key ); ?>" />
			<?php echo wp_kses_post( $option_data['value'] ); ?>
		</label>
	<?php endforeach; ?>
</fieldset>
