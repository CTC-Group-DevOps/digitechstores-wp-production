<?php

class WCML_Setup_Introduction_UI extends WCML_Setup_Step {

	const SLUG = 'introduction';

	public function get_model() {
		return [
			'strings'      => [
				'step_id'     => 'introduction_step',
				/* translators: Heading of the first step of the setup wizard. */
				'heading'     => __( "Let's make your WooCommerce store multilingual", 'woocommerce-multilingual' ),
				'description' => [

					/* translators: Line above the list of what the setup wizard will do; the list follows the colon. */
					'title' => __( 'To get started, we need to set up the following:', 'woocommerce-multilingual' ),
					/* translators: Name of one step of the setup wizard, used in the step list and as that step's heading. Verb phrase, imperative. */
					'step1' => __( "Create store pages in all your site's languages", 'woocommerce-multilingual' ),
					/* translators: Item in the list of what the setup wizard will do. Verb phrase, imperative. */
					'step2' => __( 'Choose which product attributes you want to translate', 'woocommerce-multilingual' ),
					/* translators: Item in the list of what the setup wizard will do. Verb phrase, imperative. */
					'step3' => __( 'Set your translation options', 'woocommerce-multilingual' ),
					/* translators: Item in the list of what the setup wizard will do. Verb phrase, imperative. */
					'step4' => __( 'Decide if you want to add multiple currencies to your store', 'woocommerce-multilingual' ),

				],
				/* translators: Button label that starts the setup wizard. */
				'continue'    => __( "Let's continue", 'woocommerce-multilingual' ),
				/* translators: Link that leaves the setup wizard without running it; written in the shop owner's own voice. */
				'later'       => __( "I'll do the setup later", 'woocommerce-multilingual' ),
			],
			'later_url'    => \WCML\Utilities\AdminUrl::getSetupLater(),
			'continue_url' => $this->next_step_url,
		];
	}

	public function get_template() {
		return '/setup/introduction.twig';
	}
}
