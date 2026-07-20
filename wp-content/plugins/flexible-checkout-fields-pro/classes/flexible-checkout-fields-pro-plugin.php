<?php

use WPDesk\FCF\Pro\Plugin as PluginPro;

/**
 * Class Flexible_Checkout_Fields_Pro_Plugin
 */
class Flexible_Checkout_Fields_Pro_Plugin extends \FCFProVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin implements \FCFProVendor\WPDesk\PluginBuilder\Plugin\HookableCollection {

	use \FCFProVendor\WPDesk\PluginBuilder\Plugin\HookableParent;

	/**
	 * Scripts version.
	 *
	 * @var string
	 */
	private $script_version = FLEXIBLE_CHECKOUT_FIELDS_PRO_VERSION . '.44';

	/**
	 * Renderer.
	 *
	 * @var FCFProVendor\WPDesk\View\Renderer\Renderer;
	 */
	private $renderer;

	/**
	 * Flexible_Checkout_Fields_Plugin
	 *
	 * @var Flexible_Checkout_Fields_Plugin
	 */
	private $flexible_checkout_fields_plugin;

	/**
	 * File field type.
	 *
	 * @var Flexible_Checkout_Fields_Pro_File_Field_Type
	 */
	private $file_field_type;

	/**
	 * Instance of new version main class of plugin.
	 *
	 * @var PluginPro
	 */
	private $plugin_pro;

	/**
	 * Plugin path.
	 *
	 * @var string
	 */
	protected $plugin_path;

	/**
	 * Template path.
	 *
	 * @var string
	 */
	protected $template_path;

	/**
	 * Flexible_Checkout_Fields_Pro_Plugin constructor.
	 *
	 * @param FCFProVendor\WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( FCFProVendor\WPDesk_Plugin_Info $plugin_info ) {
		parent::__construct( $plugin_info );
		$this->plugin_pro = new PluginPro( $plugin_info, $this );
	}

	/**
	 * Get flexible checkout fields plugin.
	 *
	 * @return Flexible_Checkout_Fields_Plugin
	 */
	public function get_flexible_checkout_fields_plugin() {
		if ( empty( $this->flexible_checkout_fields_plugin ) ) {
			$this->flexible_checkout_fields_plugin = flexible_checkout_fields();
		}
		return $this->flexible_checkout_fields_plugin;
	}

	/**
	 * Get script version.
	 *
	 * @return string;
	 */
	public function get_script_version() {
		return $this->script_version;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		$this->plugin_pro->hooks();
		parent::hooks();
		$this->hooks_on_hookable_objects();
	}

	/**
	 * Init base variables for plugin
	 */
	public function init_base_variables() {
		$this->plugin_url = $this->plugin_info->get_plugin_url();

		$this->plugin_path   = $this->plugin_info->get_plugin_dir();
		$this->template_path = $this->plugin_info->get_text_domain();

		$this->plugin_namespace = $this->plugin_info->get_text_domain();
		$this->template_path    = $this->plugin_info->get_text_domain();
	}

	/**
	 * Set renderer.
	 */
	private function init_renderer() {
		$resolver = new \FCFProVendor\WPDesk\View\Resolver\ChainResolver();
		$resolver->appendResolver( new \FCFProVendor\WPDesk\View\Resolver\WPThemeResolver( $this->get_template_path() ) );
		$resolver->appendResolver( new \FCFProVendor\WPDesk\View\Resolver\DirResolver( trailingslashit( $this->plugin_path ) . 'templates' ) );
		$this->renderer = new FCFProVendor\WPDesk\View\Renderer\SimplePhpRenderer( $resolver );
	}

	/**
	 * Initializes plugin functionality.
	 */
	public function init() {
		$this->plugin_pro->load_action_init();
	}

	/**
	 * Initializes plugin functionality after "flexible_checkout_fields/init" action.
	 */
	public function load_after_action_init() {
		$this->init_base_variables();

		$this->init_renderer();

		$checkout_fields_pro = new Flexible_Checkout_Fields_Pro( $this );
		$this->add_hookable( $checkout_fields_pro );

		$this->add_hookable( new Flexible_Checkout_Fields_Order_Metabox( $checkout_fields_pro, $this ) );

		$this->plugin_pro->init();
		parent::init();
	}

	/**
	 * Links filter.
	 *
	 * @param array $links Links.
	 * @return array
	 */
	public function links_filter( $links ) {
		$plugin_links = [
			'<a href="' . admin_url( 'admin.php?page=wpdesk_checkout_fields_settings' ) . '">' . __( 'Settings', 'flexible-checkout-fields-pro' ) . '</a>',
			'<a href="' . esc_url( apply_filters( 'flexible_checkout_fields/short_url', '#', 'fcf-settings-row-action-docs' ) ) . '" target="_blank">' . __( 'Docs', 'flexible-checkout-fields-pro' ) . '</a>',
			'<a href="' . esc_url( apply_filters( 'flexible_checkout_fields/short_url', '#', 'fcf-settings-row-action-support-pro' ) ) . '" target="_blank">' . __( 'Support', 'flexible-checkout-fields-pro' ) . '</a>',
		];

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Renders end returns selected template
	 *
	 * @param string $name Name of the template.
	 * @param string $path Additional inner path to the template.
	 * @param array  $args args Accessible from template.
	 *
	 * @return string
	 */
	public function load_template( $name, $path = '', $args = [] ) {
		if ( '' !== $path ) {
			$template = trailingslashit( $path ) . $name;
		} else {
			$template = $name;
		}
		return $this->renderer->render( $template, $args );
	}

	/**
	 * Should enqueue admin scripts.
	 * Script should be loaded on FCF Settings and order edit.
	 *
	 * @return bool
	 */
	private function should_enqueue_admin_scripts() {
		$current_screen = get_current_screen();
		if ( isset( $current_screen )
			&& ( in_array(
				$current_screen->id,
				[
					'shop_order',
					'shop_subscription',
				],
				true
			) )
		) {
			return true;
		}
		return false;
	}

	/**
	 * Get template path.
	 *
	 * @return string
	 */
	public function get_template_path() {
		return trailingslashit( $this->template_path );
	}
}
