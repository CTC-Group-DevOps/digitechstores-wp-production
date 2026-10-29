<?php

namespace WCML\Compatibility\WpAllImport;

class MultiCurrency implements \IWPML_Backend_Action, \IWPML_Frontend_Action {

	private $isImportRunning = false;

	public function add_hooks() {
		add_filter( 'wcml_is_importing', [ $this, 'is_importing' ] );

		add_action( 'pmxi_before_xml_import', [ $this, 'startImport' ] );
		add_action( 'pmxi_after_xml_import', [ $this, 'stopImport' ] );
	}

	public function startImport() {
		$this->isImportRunning = true;
	}

	public function stopImport() {
		$this->isImportRunning = false;
	}

	public function is_importing( $is_importing ) {
		return $is_importing
			|| $this->isImportRunning
			|| ( class_exists( \Wpai\WordPress\AttachmentHandler::class )
				&& property_exists( \Wpai\WordPress\AttachmentHandler::class, 'importRecord' )
				&& ! empty( \Wpai\WordPress\AttachmentHandler::$importRecord ) );
	}
}
