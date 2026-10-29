<?php

namespace WCML\Setup;

use WCML\Media\Wrapper\Factory as MediaFactory;
use function WCML\functions\getSitePress;
use function WCML\functions\isStandAlone;

class CostEstimateContributorsFactory implements \IWPML_Backend_Action_Loader, \IWPML_Frontend_Action_Loader {

	public function create() {
		if ( isStandAlone() || getSitePress()->is_setup_complete() ) {
			return null;
		}

		global $woocommerce_wpml, $wpdb, $wpml_post_translations, $wpml_term_translations;

		$sitepress = getSitePress();

		if ( null === $woocommerce_wpml->attributes ) {
			$woocommerce_wpml->attributes = new \WCML_Attributes(
				$woocommerce_wpml,
				$sitepress,
				$wpml_post_translations,
				$wpml_term_translations,
				$wpdb
			);
		}

		if ( null === $woocommerce_wpml->sync_variations_data ) {
			$woocommerce_wpml->sync_variations_data = new \WCML_Synchronize_Variations_Data(
				$woocommerce_wpml,
				$sitepress,
				$wpdb
			);
		}

		if ( null === $woocommerce_wpml->media ) {
			$woocommerce_wpml->media = MediaFactory::create();
		}

		return new CostEstimateContributors(
			new \WCML_TP_Support( $woocommerce_wpml, $wpdb, new \WPML_Element_Translation_Package() )
		);
	}
}
