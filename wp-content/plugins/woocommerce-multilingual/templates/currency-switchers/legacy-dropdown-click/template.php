<?php
?>
<div class="<?php echo esc_attr( $css_classes ); ?> js-wcml-dropdown-click">
	<ul>
		<li class="wcml-cs-active-currency">
			<button type="button" class="js-wcml-dropdown-click-toggle wcml-cs-item-toggle" aria-current="true" aria-haspopup="true" aria-expanded="false"><?php echo wp_kses_post( WCML_Currency_Switcher_Template::get_formatted_price( $selected_currency, $format ) ); ?></button>
			<ul class="wcml-cs-submenu js-wcml-dropdown-click-submenu">
				<?php foreach ( $currencies as $currency ) : ?>
					<?php if ( $currency != $selected_currency ) : ?>
						<li>
							<button type="button" data-currency="<?php echo esc_attr( $currency ); ?>"><?php echo wp_kses_post( WCML_Currency_Switcher_Template::get_formatted_price( $currency, $format ) ); ?></button>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</div>