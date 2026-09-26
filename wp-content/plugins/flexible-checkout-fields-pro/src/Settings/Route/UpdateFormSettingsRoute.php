<?php

namespace WPDesk\FCF\Pro\Settings\Route;

use WPDesk\FCF\Free\Collections\RouteParamBag;
use WPDesk\FCF\Free\Settings\Route\RouteInterface;
use WPDesk\FCF\Pro\Settings\Form\SettingsPageForm;
use WPDesk\FCF\Free\Settings\Route\UpdateFormSettingsRoute as DefaultUpdateFormSettingsRoute;

/**
 * {@inheritdoc}
 */
class UpdateFormSettingsRoute extends DefaultUpdateFormSettingsRoute implements RouteInterface {

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Exception
	 */
	public function get_endpoint_response( RouteParamBag $params ) {
		try {
			$status = ( new SettingsPageForm() )->save_form_data( $params->toArray() );
			if ( $status !== true ) {
				throw new \Exception();
			}

			return null;
		} catch ( \Exception $e ) {
			throw $e;
		}
	}
}
