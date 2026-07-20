<?php

namespace WPDesk\FCF\Pro;

use WPDesk\FCF\Pro\Form;
use WPDesk\FCF\Pro\Pricing;
use WPDesk\FCF\Pro\Validator;
use WPDesk\FCF\Pro\Integration;
use FCFProVendor\WPDesk_Plugin_Info;
use WPDesk\FCF\Pro\Plugin\Compatibility;
use WPDesk\FCF\Free\Settings\Form\EditFieldsForm;
use WPDesk\FCF\Free\Settings\Route\RouteIntegration;
use FCFProVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use FCFProVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use FCFProVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProvider;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Route\RuleValuesRoute;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Route\RuleCategoryRoute;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Route\RuleSelectionRoute;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Route\RuleComparisonRoute;
use WPDesk\FCF\Pro\ConditionalLogic\Settings\Rule\OptionsProviderRegistry;
use FCFProVendor\WPDesk\Library\PluginUpdateReminder\RemindersFactory;

/**
 * Main plugin class. The most important flow decisions are made here.
 */
class Plugin extends AbstractPlugin implements HookableCollection {

	const ROUTE_NAMESPACE = 'flexible-checkout-fields-pro/v1';

	use HookableParent;

	/**
	 * Scripts version.
	 *
	 * @var string
	 */
	private $script_version = '1';

	/**
	 * Instance of old version main class of plugin.
	 *
	 * @var \Flexible_Checkout_Fields_Pro_Plugin
	 */
	private $plugin_old;

	/**
	 * Plugin constructor.
	 *
	 * @param WPDesk_Plugin_Info                   $plugin_info Plugin info.
	 * @param \Flexible_Checkout_Fields_Pro_Plugin $plugin_old  Main plugin.
	 */
	public function __construct( WPDesk_Plugin_Info $plugin_info, \Flexible_Checkout_Fields_Pro_Plugin $plugin_old ) {
		parent::__construct( $plugin_info );

		$this->plugin_url       = $this->plugin_info->get_plugin_url();
		$this->plugin_namespace = $this->plugin_info->get_text_domain();
		$this->script_version   = $plugin_info->get_version();
		$this->plugin_old       = $plugin_old;
	}

	/**
	 * Initializes plugin external state after "flexible_checkout_fields/init" action.
	 * In case of compatibility problems, displays Admin Notices.
	 *
	 * @return void
	 */
	public function load_action_init() {
		add_action(
			'flexible_checkout_fields/init',
			function ( $integrator ) {
				$compatibility = new Compatibility();
				$compatibility->set_plugin( $this );

				if ( ! $compatibility->check_plugin_compatibility( $integrator ) ) {
					add_filter( 'flexible_checkout_fields/is_pro_compatible', 'return_false' );
					return;
				}

				$this->plugin_old->load_after_action_init();

				$settings = \get_option( EditFieldsForm::SETTINGS_OPTION_NAME, [] );

				( new Form\Assets( $this->plugin_info, $settings ) )->hooks();

				$rule_fields_options = new OptionsProviderRegistry();
				$rule_fields_options->register( new OptionsProvider\CartContains() );
				$rule_fields_options->register( new OptionsProvider\Cart() );
				$rule_fields_options->register( new OptionsProvider\FlexibleCheckoutFields( $settings ) );
				$rule_fields_options->register( new OptionsProvider\Shipping() );
				$rule_fields_options->register( new OptionsProvider\WooFields() );
				$rule_fields_options->register( new OptionsProvider\User() );
				$rule_fields_options->register( new OptionsProvider\Payment() );
				$rule_fields_options->register( new OptionsProvider\Date() );
				$rule_fields_options->register( new OptionsProvider\FlexibleProductFields() );

				// add routes
				( new RouteIntegration( new RuleCategoryRoute( $rule_fields_options ) ) )->hooks();
				( new RouteIntegration( new RuleSelectionRoute( $rule_fields_options ) ) )->hooks();
				( new RouteIntegration( new RuleComparisonRoute( $rule_fields_options ) ) )->hooks();
				( new RouteIntegration( new RuleValuesRoute( $rule_fields_options ) ) )->hooks();

				( new ConditionalLogic\Settings\Rule\RuleFieldsSettings( $rule_fields_options ) )->hooks();
				( new ConditionalLogic\ResultsProcessor() )->hooks();
			}
		);
	}

	/**
	 * Initializes plugin external state.
	 * The plugin internal state is initialized in the constructor and the plugin should be internally consistent after
	 * creation. The external state includes hooks execution, communication with other plugins, integration with WC
	 * etc.
	 *
	 * @return void
	 */
	public function init() {
		( new Field\Types() )->init();
		$this->add_hookable( new Integration\PolylangIntegrator( $this->plugin_info ) );
		$this->add_hookable( new Integration\WpmlIntegrator( $this->plugin_info ) );
		$this->add_hookable( new Field\FieldCustomAttrs() );
		$this->add_hookable( new Field\FieldTemplateLoader( $this->plugin_info ) );
		$this->add_hookable( new Field\File\RestRouteCreator() );
		$this->add_hookable( new Field\File\PreviewGenerator() );
		$this->add_hookable( new Field\File\FieldValuePrinter() );
		$this->add_hookable( new Field\FieldValuePrinter() );

		$this->add_hookable( new Pricing\Fields() );
		$this->add_hookable( new Pricing\Session() );
		( new Settings\Forms() )->init();
		( new Settings\Routes() )->init();
		$this->add_hookable( new Validator\FieldValidator() );

		$this->add_hookable(
			new RemindersFactory(
				$this->plugin_info->get_plugin_dir(),
				$this->plugin_info->get_plugin_file_name(),
				$this->plugin_info->get_plugin_name()
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		$this->hooks_on_hookable_objects();
	}

	/**
	 * Get script version.
	 *
	 * @return string;
	 */
	public function get_script_version() {
		return $this->script_version;
	}
}
