<?php
?>
<div class="<?php echo esc_attr( $css_classes ); ?>">
	<ul>
		<?php foreach ( $currencies as $currency ) : ?>
			<li <?php echo ( $currency == $selected_currency ) ? 'class="wcml-cs-active-currency"' : ''; ?>>
				<button type="button" data-currency="<?php echo esc_attr( $currency ); ?>"<?php echo ( $currency === $selected_currency ) ? ' aria-current="true"' : ''; ?>><?php echo wp_kses_post( WCML_Currency_Switcher_Template::get_formatted_price( $currency, $format ) ); ?></button>
			</li>
		<?php endforeach; ?>
	</ul>
</div>