<?php

namespace WCML\Terms\Count;

use WCML\Terms\SuspendWpmlFiltersFactory;
use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\LIB\WP\Hooks as WpHooks;
use WCML\Utilities\WCTaxonomies;
use function WPML\FP\spreadArgs;

class Hooks implements \IWPML_Backend_Action, \IWPML_REST_Action {

	private const RECOUNT_TOOL = 'recount_terms';

	private const PRIORITY_BEFORE_WC_HANDLER = 1;

	private const PRIORITY_AFTER_RECOUNT = PHP_INT_MAX;

	public function add_hooks() {
		WpHooks::onFilter( 'woocommerce_product_recount_terms', PHP_INT_MAX )
			->then( spreadArgs( [ self::class, 'disableTermFilters' ] ) );

		WpHooks::onAction( 'icl_save_term_translation', 10, 2 )
			->then( spreadArgs( [ self::class, 'recountOnSaveTermTranslation' ] ) );

		WpHooks::onAction( 'wpml_sync_term_hierarchy_done' )
			->then( [ self::class, 'recountAllTermsInShutdown' ] );

		WpHooks::onAction( 'woocommerce_page_wc-status', self::PRIORITY_BEFORE_WC_HANDLER )
			->then( [ self::class, 'suspendTermFiltersOnRecountToolExecution' ] );

		WpHooks::onFilter( 'rest_dispatch_request', 10, 4 )
			->then( spreadArgs( [ self::class, 'suspendTermFiltersOnRecountToolRequest' ] ) );

		WpHooks::onAction( 'woocommerce_settings_save_products', self::PRIORITY_BEFORE_WC_HANDLER )
			->then( [ self::class, 'suspendTermFiltersOnProductSettingsSave' ] );
	}

	public static function disableTermFilters( $shouldRecountTerms ) {
		if ( $shouldRecountTerms ) {
			$filtersSuspend = SuspendWpmlFiltersFactory::create();

			$restore = function() use ( &$restore, $filtersSuspend ) {
				remove_action( 'delete_transient_wc_term_counts', $restore );
				$filtersSuspend->resume();
			};

			add_action( 'delete_transient_wc_term_counts', $restore );

		}

		return $shouldRecountTerms;
	}

	public static function recountOnSaveTermTranslation( $originalTax, $translatedTerm ) {
		$taxonomyName = Obj::prop( 'taxonomy', $originalTax );

		if ( in_array( $taxonomyName, [ WCTaxonomies::TAXONOMY_PRODUCT_CATEGORY, WCTaxonomies::TAXONOMY_PRODUCT_TAG ], true ) ) {
			SuspendWpmlFiltersFactory::create()->runAndResume( function () use ( $translatedTerm, $taxonomyName ) {
				_wc_term_recount( [ (int) Obj::prop( 'term_taxonomy_id', $translatedTerm ) ], get_taxonomy( $taxonomyName ) );
			} );
		}
	}

	public static function recountAllTermsInShutdown() {
		WpHooks::onAction( 'shutdown' )->then( Fns::once( [ self::class, 'recountAllTerms' ] ) );
	}

	public static function recountAllTerms() {
		SuspendWpmlFiltersFactory::create()->runAndResume( function() {
			wc_recount_all_terms();
		} );
	}

	public static function suspendTermFiltersOnRecountToolExecution() {
		$nonce = isset( $_REQUEST['_wpnonce'] ) && is_string( $_REQUEST['_wpnonce'] )
			? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) )
			: '';

		if ( '' === $nonce || ! wp_verify_nonce( $nonce, 'debug_action' ) ) {
			return;
		}

		$isRecountTool = isset( $_GET['tab'], $_GET['action'] )
			&& 'tools' === sanitize_key( wp_unslash( $_GET['tab'] ) )
			&& self::RECOUNT_TOOL === sanitize_key( wp_unslash( $_GET['action'] ) );

		if ( $isRecountTool ) {
			self::suspendTermFiltersUntil( 'woocommerce_system_status_tool_executed' );
		}
	}

	public static function suspendTermFiltersOnRecountToolRequest( $dispatchResult, $request, $route, $handler ) {
		$isRecountTool = in_array( $request->get_method(), [ 'POST', 'PUT', 'PATCH' ], true )
			&& false !== strpos( $route, '/system_status/tools/' )
			&& self::RECOUNT_TOOL === $request->get_param( 'id' );

		if ( $isRecountTool ) {
			self::suspendTermFiltersUntil( 'woocommerce_rest_insert_system_status_tool' );
		}

		return $dispatchResult;
	}

	public static function suspendTermFiltersOnProductSettingsSave() {
		self::suspendTermFiltersUntil( 'woocommerce_settings_save_products' );
	}

	private static function suspendTermFiltersUntil( $resumeHook ) {
		$filtersSuspend = SuspendWpmlFiltersFactory::create();

		$restore = function() use ( &$restore, $filtersSuspend, $resumeHook ) {
			remove_action( $resumeHook, $restore, self::PRIORITY_AFTER_RECOUNT );
			$filtersSuspend->resume();
		};

		add_action( $resumeHook, $restore, self::PRIORITY_AFTER_RECOUNT );
	}
}
