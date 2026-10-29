<?php

namespace WCML\AdminDashboard;

use WPML\Core\ISitePress;
use WCML\StandAlone\NullSitePress;
use SitePress;
use wpdb;
use IWPML_DIC_Action;
use IWPML_Backend_Action;

class Hooks implements IWPML_Backend_Action, IWPML_DIC_Action {

	private $sitepress;

	private $wpdb;

	public function __construct( ISitePress $sitepress, wpdb $wpdb ) {
		$this->sitepress = $sitepress;
		$this->wpdb      = $wpdb;
	}

	public function add_hooks() {
		add_action( 'wp_dashboard_setup', [ $this, 'clearStockTransients' ] );
		add_filter( 'woocommerce_status_widget_low_in_stock_count_pre_query', [ $this, 'lowInStockCount' ], 10, 3 );
		add_filter( 'woocommerce_status_widget_out_of_stock_count_pre_query', [ $this, 'outOfStockCount' ], 10, 2 );
	}

	public function clearStockTransients() {
		delete_transient( 'wc_outofstock_count' );
		delete_transient( 'wc_low_stock_count' );
	}

	public function lowInStockCount( $count, $stock, $nostock ) {
		return $this->countInCurrentLanguage( $count, 'lookup.stock_quantity <= %d AND lookup.stock_quantity > %d', [ $stock, $nostock ] );
	}

	public function outOfStockCount( $count, $nostock ) {
		return $this->countInCurrentLanguage( $count, 'lookup.stock_quantity <= %d', [ $nostock ] );
	}

	private function countInCurrentLanguage( $count, $stockCondition, array $stockArgs ) {
		$currentLanguage = $this->sitepress->get_current_language();

		if ( 'all' === $currentLanguage ) {
			return $count;
		}

		$sql = "SELECT COUNT( lookup.product_id )
			FROM {$this->wpdb->prefix}wc_product_meta_lookup AS lookup
			INNER JOIN {$this->wpdb->posts} AS posts ON lookup.product_id = posts.ID
			INNER JOIN {$this->wpdb->prefix}icl_translations AS t
				ON posts.ID = t.element_id AND t.element_type IN ( 'post_product', 'post_product_variation' )
			WHERE " . $stockCondition . "
			AND posts.post_status = 'publish'
			AND t.language_code = %s";

		$result = $this->wpdb->get_var( $this->wpdb->prepare( $sql, array_merge( $stockArgs, [ $currentLanguage ] ) ) );

		return (int) $result;
	}
}
