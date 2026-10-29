<?php

class WCML_Not_Supported_Payment_Gateway extends WCML_Payment_Gateway{

	public function get_output_model() {
		return [
			'id'          => $this->get_id(),
			'title'       => $this->get_title(),
			'isSupported' => false,
			'settings'    => [],
			'tooltip'     => '',
			'strings'     => [
				/* translators: Status shown next to a payment method WPML cannot yet set currencies for. */
				'labelNotYetSupported' => __( 'Not yet supported', 'woocommerce-multilingual' ),
			],
		];
	}
}