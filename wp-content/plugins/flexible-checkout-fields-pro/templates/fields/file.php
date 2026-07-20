<?php
/**
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields/fields/file.php
 *
 * @var string   $key               Field ID.
 * @var mixed[]  $args              Custom attributes for field.
 * @var mixed    $value             Field value.
 * @var string[] $custom_attributes .
 *
 * @package Flexible Checkout Fields PRO
 */

use WPDesk\FCF\Pro\Field\File\RestRouteCreator;

$value_filenames = apply_filters( 'flexible_checkout_fields_print_value_raw', $value, $args );
$value           = array_filter( (array) json_decode( $value, true ) ?: [] );

?>

<p class="form-row <?php echo esc_attr( $args['class'] ); ?>"
	id="<?php echo esc_attr( $key ); ?>_field"
	data-priority="<?php echo esc_attr( $args['priority'] ); ?>"
	data-fcf-field="<?php echo esc_attr( $key ); ?>">
	<label for="<?php echo esc_attr( $key ); ?>">
		<?php echo wp_kses_post( $args['label'] ); ?>
		<?php if ( $args['required'] ) : ?>
			<abbr class="required"
				title="<?php echo esc_attr( __( 'Required Field', 'flexible-checkout-fields-pro' ) ); ?>">*</abbr>
		<?php else : ?>
			<span class="optional">(<?php echo esc_html__( 'optional', 'woocommerce' ); ?>)</span>
		<?php endif; ?>
	</label>
	<span class="fcf-file-items"
		data-api-url="<?php echo esc_url( RestRouteCreator::get_route_url() ); ?>"
		data-api-error-code="<?php echo esc_attr( RestRouteCreator::RESPONSE_ERROR_CODE ); ?>"
		data-api-error-message="<?php echo esc_attr( __( 'An unknown error has occurred. Try again.', 'flexible-checkout-fields-pro' ) ); ?>"
		data-field-name="<?php echo esc_attr( $key ); ?>">
		<?php for ( $index = 0; $index < ( $args['files_limit'] ?? 1 ); $index++ ) : ?>
			<span class="fcf-file-item"
				data-index="<?php echo esc_attr( $index ); ?>"
				<?php echo ( $index > count( $value_filenames ) ) ? 'hidden' : ''; ?>>
				<span class="fcf-file-draggable">
					<input type="file"
						class="fcf-file-draggable-input"
						accept="<?php echo esc_attr( $args['file_types'] ); ?>">
					<span class="fcf-file-draggable-content">
						<span class="fcf-file-draggable-error" hidden></span>
						<span class="fcf-file-draggable-placeholder"
							<?php echo ( $index < count( $value_filenames ) ) ? 'hidden' : ''; ?>>
							<?php echo esc_html__( 'Select File or Drag & Drop', 'flexible-checkout-fields-pro' ); ?>
						</span>
						<span class="fcf-file-draggable-loading" hidden>
							<?php echo esc_html__( 'Uploading...', 'flexible-checkout-fields-pro' ); ?>
						</span>
						<span class="fcf-file-draggable-preview"
							<?php echo ( $index >= count( $value_filenames ) ) ? 'hidden' : ''; ?>>
							<?php echo wp_kses_post( $value_filenames[ $index ] ?? '' ); ?>
						</span>
					</span>
					<button type="button"
						class="button fcf-file-draggable-delete"
						<?php echo ( $index >= count( $value_filenames ) ) ? 'hidden' : ''; ?>>
						<?php echo esc_html__( 'Delete', 'flexible-checkout-fields-pro' ); ?>
					</button>
				</span>
				<input type="hidden"
					name="<?php echo esc_attr( $key ); ?>[]"
					value="<?php echo esc_attr( $value[ $index ] ?? '' ); ?>"
					class="fcf-file-draggable-value fcf-input-field"
					<?php echo ( ( $value[ $index ] ?? '' ) === '' ) ? 'disabled' : ''; ?>>
			</span>
		<?php endfor; ?>
	</span>
</p>
