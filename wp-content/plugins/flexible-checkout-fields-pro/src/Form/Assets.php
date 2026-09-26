<?php

namespace WPDesk\FCF\Pro\Form;

use FCFProVendor\WPDesk_Plugin_Info;
use WPDesk\FCF\Pro\ConditionalLogic;
use FCFProVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FCF\Pro\ConditionalLogic\ResultsProcessor;

/**
 * Initiates loading of assets required to handle the form.
 */
class Assets implements Hookable {

	const ASSETS_HANDLE_PATTERN = 'fcf-pro-assets-%s';

	private const CURRENT_ADMIN_PAGE_WHEN_HPOS = 'woocommerce_page_wc-orders';

	/**
	 * @var WPDesk_Plugin_Info
	 */
	private $plugin_info;

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( WPDesk_Plugin_Info $plugin_info, array $settings ) {
		$this->plugin_info = $plugin_info;
		$this->settings    = $settings;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'load_front_assets' ] );
		add_action( 'admin_print_scripts-post.php', [ $this, 'load_admin_order_assets' ] );
		add_action( 'admin_print_scripts-post-new.php', [ $this, 'load_admin_order_assets' ] );
		add_action( 'admin_print_scripts-profile.php', [ $this, 'load_checkout_assets' ] );
		// when HPOS is enabled, the current admin page slug differs.
		add_action( 'admin_print_scripts-' . self::CURRENT_ADMIN_PAGE_WHEN_HPOS, [ $this, 'load_checkout_assets' ] );
	}

	/**
	 * @return void
	 *
	 * @internal
	 */
	public function load_front_assets() {
		if ( ! is_checkout() && ! is_account_page() ) {
			return;
		}

		$this->load_checkout_assets();
		$this->localize_assets_for_conditional_logic();
	}

	/**
	 * @return void
	 *
	 * @internal
	 */
	public function load_admin_order_assets() {
		global $post_type;
		if ( $post_type !== 'shop_order' ) {
			return;
		}

		$this->load_checkout_assets();
	}

	/**
	 * @return void
	 *
	 * @internal
	 */
	public function load_checkout_assets() {
		wp_enqueue_style(
			sprintf( self::ASSETS_HANDLE_PATTERN, 'new-admin-css' ),
			sprintf( '%1$s/assets/css/new-front.css', untrailingslashit( $this->plugin_info->get_plugin_url() ) ),
			[],
			$this->plugin_info->get_version()
		);

		wp_enqueue_script(
			sprintf( self::ASSETS_HANDLE_PATTERN, 'new-admin-js' ),
			sprintf( '%1$s/assets/js/new-front.js', untrailingslashit( $this->plugin_info->get_plugin_url() ) ),
			[ 'jquery', 'wp-i18n' ],
			$this->plugin_info->get_version(),
			true
		);

		add_action( 'wp_footer', [ $this, 'load_datepicker_localize' ], 0 );
	}

	/**
	 * @return void
	 *
	 * @internal
	 */
	public function load_datepicker_localize() {
		$locales = [
			'days'        => explode( ',', __( 'Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday', 'flexible-checkout-fields-pro' ) ),
			'daysShort'   => explode( ',', __( 'Sun,Mon,Tue,Wed,Thu,Fri,Sat', 'flexible-checkout-fields-pro' ) ),
			'daysMin'     => explode( ',', __( 'Su,Mo,Tu,We,Th,Fr,Sa', 'flexible-checkout-fields-pro' ) ),
			'months'      => explode( ',', __( 'January,February,March,April,May,June,July,August,September,October,November,December', 'flexible-checkout-fields-pro' ) ),
			'monthsShort' => explode( ',', __( 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec', 'flexible-checkout-fields-pro' ) ),
			'today'       => __( 'Today', 'flexible-checkout-fields-pro' ),
			'clear'       => __( 'Clear', 'flexible-checkout-fields-pro' ),
			'titleFormat' => 'MM y',
			'format'      => 'mm/dd/yyyy',
			'weekstart'   => 0,
		];
		?>
		<script>
			window.fcf_pro_datepicker_locales = <?php echo json_encode( $locales ); ?>
		</script>
		<?php
	}

	public function localize_assets_for_conditional_logic() {
		$settings_formatter = new ConditionalLogic\SettingsFormatter(
			$this->settings,
			new ConditionalLogic\RuleResolverFactory()
		);

		$script_name = sprintf( self::ASSETS_HANDLE_PATTERN, 'new-admin-js' );

		\wp_localize_script(
			$script_name,
			'fcfConditionalLogic',
			[
				'allRules'              => $settings_formatter->get_all_rules(),
				'rulesRelations'        => $settings_formatter->get_rules_relations(),
				'fieldsActions'         => $settings_formatter->get_fields_actions(),
				'resultStorageFieldId'  => ResultsProcessor::RESULTS_STORAGE_FIELD_ID,
			]
		);
	}
}
