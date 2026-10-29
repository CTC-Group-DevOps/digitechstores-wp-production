<?php

namespace WCML\Utilities;

use WCML_Admin_Menus;
use WCML_Capabilities;
use WPML\FP\Lst;
use WPML\FP\Obj;
use WPML\FP\Relation;
use function sanitize_key;
use function WCML\functions\getSetting;
use function WCML\functions\isStandAlone;

class AdminPages {

	const TAB_MULTICURRENCY = 'multi-currency';
	const TAB_MULTILINGUAL  = 'multilingual';
	const TAB_MULTILINGUAL_STANDALONE  = 'multilingual-standalone';

	const BONDED_SETTINGS_PAGE     = 'tm/menu/settings';
	const BONDED_TRANSLATIONS_PAGE = 'tm/menu/main.php';
	const BONDED_SUPPORT_PAGE      = 'sitepress-multilingual-cms/menu/support.php';

	const BONDED_SUPPORT_TOOLS = [
		'wcml-status',
		'wcml-fix-translations',
		'wcml-config-log',
		'wcml-translation-maintenance',
	];

	public static function getDefaultTab() {
		return isStandAlone() ? self::TAB_MULTICURRENCY : self::TAB_MULTILINGUAL;
	}

	public static function getCurrentTab( $fallback = null ) {
		return sanitize_key( Obj::prop( 'tab', $_GET ) ) ?: $fallback;
	}

	public static function getTabToDisplay() {
		return WCML_Capabilities::canAccessAllWcmlTabs()
			? self::getCurrentTab( self::getDefaultTab() )
			: self::getDefaultTab();
	}

	public static function isTab( $tabs ) {
		return Lst::includes( self::getCurrentTab(), (array) $tabs );
	}

	public static function isPage( $page ) {
		return Relation::propEq( 'page', $page, $_GET );
	}

	public static function isWcmlSettings() {
		return self::isPage( WCML_Admin_Menus::SLUG );
	}

	public static function isMultiCurrency() {
		$tabs = [ self::TAB_MULTICURRENCY ];

		if ( isStandAlone() ) {
			$tabs[] = null;
		}

		return self::isWcmlSettings() && self::isTab( $tabs );
	}

	public static function isBondedMultiCurrency() {
		return self::isBondedMode()
			&& self::isPage( self::BONDED_SETTINGS_PAGE )
			&& Relation::propEq( 'section', self::TAB_MULTICURRENCY, $_GET );
	}

	public static function rendersMultiCurrencyBody(): bool {
		if ( isStandAlone() ) {
			return self::isMultiCurrency();
		}

		if ( self::isBondedMode() ) {
			return self::isBondedMultiCurrency();
		}

		return self::isMultiCurrency() && self::hasRunSetupWizard();
	}

	private static function hasRunSetupWizard(): bool {
		return (bool) getSetting( 'set_up_wizard_run' );
	}

	public static function isBondedWcmlPage() {
		if ( ! self::isBondedMode() ) {
			return false;
		}

		if ( self::isPage( self::BONDED_TRANSLATIONS_PAGE ) ) {
			return Relation::propEq( 'tab', 'store-urls', $_GET );
		}

		if ( self::isPage( self::BONDED_SETTINGS_PAGE ) ) {
			return Lst::includes( Obj::prop( 'section', $_GET ), [ 'wcml', self::TAB_MULTICURRENCY ] );
		}

		if ( self::isPage( self::BONDED_SUPPORT_PAGE ) ) {
			return Lst::includes( Obj::prop( 'tool', $_GET ), self::BONDED_SUPPORT_TOOLS );
		}

		return false;
	}

	private static function isBondedMode() {
		return class_exists( '\WCML_Bonded_Menu' ) && \WCML_Bonded_Menu::is_active();
	}

	public static function isTranslationQueue() {
		return ! isStandAlone() && self::isTmPage( '/menu/translations-queue.php' );
	}

	public static function isTranslationsDashboard() {
		return ! isStandAlone() && self::isTmPage( '/menu/main.php' );
	}

	public static function isWcProductAttributesPage(): bool {
		return self::isPage( 'product_attributes' ) && Relation::propEq( 'post_type', 'product', $_GET );
	}

	private static function isTmPage( $path ) {
		return defined( 'WPML_TM_FOLDER' ) && self::isPage( WPML_TM_FOLDER . $path );
	}
}
