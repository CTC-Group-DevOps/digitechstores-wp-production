<?php

namespace WPDesk\FCF\Pro\Field;

use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FCFProVendor\WPDesk_Plugin_Info;

/**
 * .
 */
class FieldTemplateLoader implements Hookable {

	/**
	 * @var WPDesk_Plugin_Info .
	 */
	private $plugin_info;

	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_filter( 'flexible_checkout_fields/templates_paths', [ $this, 'add_templates_path' ] );
	}

	/**
	 * @param string[] $paths .
	 *
	 * @return string[]
	 */
	public function add_templates_path( array $paths ): array {
		return array_merge(
			[
				$this->plugin_info->get_plugin_dir() . '/templates',
			],
			$paths
		);
	}
}
