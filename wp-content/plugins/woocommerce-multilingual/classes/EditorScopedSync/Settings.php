<?php

namespace WCML\EditorScopedSync;

use WCML\PostHog\Event\EditorScopedSyncSettingChanged;
use WPML\PostHog\Event\CaptureEvent;

class Settings implements \IWPML_Backend_Action {

	const FIELD_SAVE_MODE = 'wcml_editor_save_mode';

	private $mode;

	public function __construct( Mode $mode ) {
		$this->mode = $mode;
	}

	public function add_hooks() {
		add_action( 'wcml_settings_before_synchronization', [ $this, 'renderSection' ] );

		add_action( 'wcml_settings_form_saved', [ $this, 'maybeSaveOnWcmlSubmit' ] );
	}

	public function maybeSaveOnWcmlSubmit() {
		$nonce = isset( $_POST['wcml_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wcml_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'wcml_save_settings_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'wpml_operate_woocommerce_multilingual' ) && ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$value = Mode::VALUE_EDITOR_SCOPED === sanitize_text_field( wp_unslash( $_POST[ self::FIELD_SAVE_MODE ] ?? '' ) )
			? Mode::VALUE_EDITOR_SCOPED
			: Mode::VALUE_COMPLETE;

		if ( $value === $this->mode->current() ) {
			return;
		}

		$this->mode->set( $value );

		try {
			CaptureEvent::capture(
				new EditorScopedSyncSettingChanged( [ 'mode' => $value ] )
			);
		} catch ( \Throwable $e ) {
		}
	}

	public function renderSection() {
		$mode = $this->mode->current();
		?>
		<div class="wcml-section">
			<div class="wcml-section-header">
				<h3>
					<?php /* translators: Heading of the section on the plugin's Settings page that controls how variations are synced. */ esc_html_e( 'Variation translation sync on product save', 'woocommerce-multilingual' ); ?>
					<i class="otgs-ico-help wcml-tip" data-tip="<?php echo /* translators: Tooltip of the help icon next to the variation-sync heading on the Settings page. "Translation Dashboard" and "Translate Everything Automatically" are WPML screen names. */ esc_attr__( 'Choose how WPML updates variation translations when you save a variable product. Affects saves in the WooCommerce product editor only — Translation Dashboard and Translate Everything Automatically always run a complete sync.', 'woocommerce-multilingual' ); ?>"></i>
				</h3>
			</div>
			<div class="wcml-section-content">
				<p style="margin:0 0 8px;color:#3c434a;">
					<?php /* translators: First line under the variation-sync heading on the Settings page. */ esc_html_e( 'Choose how WPML updates variation translations when you save a variable product.', 'woocommerce-multilingual' ); ?>
				</p>
				<p style="margin:0 0 12px;color:#646970;font-style:italic;font-size:13px;">
					<?php echo wcml_bold_names( /* translators: Second line under the variation-sync heading on the Settings page. Keep the bold tags around the WPML screen names. */ __( 'Affects saves in the WooCommerce product editor only. <b>Translation Dashboard</b> and <b>Translate Everything</b> Automatically always run a complete sync.', 'woocommerce-multilingual' ) ); ?>
				</p>
				<ul>
					<li>
						<input type="radio" name="<?php echo esc_attr( self::FIELD_SAVE_MODE ); ?>"
							value="<?php echo esc_attr( Mode::VALUE_COMPLETE ); ?>"
							<?php checked( $mode, Mode::VALUE_COMPLETE ); ?>
							id="wcml_editor_save_mode_complete" />
						<label for="wcml_editor_save_mode_complete">
							<strong><?php /* translators: Label of the first radio button of the variation-sync setting. Verb phrase, imperative. */ esc_html_e( 'Always check all variations', 'woocommerce-multilingual' ); ?></strong><br/>
							<span style="color:#3c434a;">
								<?php /* translators: Explanation under the first radio button of the variation-sync setting. */ esc_html_e( "On every save, WPML checks every variation in every language and syncs anything that's out of date.", 'woocommerce-multilingual' ); ?>
							</span>
						</label>
					</li>
					<li>
						<input type="radio" name="<?php echo esc_attr( self::FIELD_SAVE_MODE ); ?>"
							value="<?php echo esc_attr( Mode::VALUE_EDITOR_SCOPED ); ?>"
							<?php checked( $mode, Mode::VALUE_EDITOR_SCOPED ); ?>
							id="wcml_editor_save_mode_editor_scoped" />
						<label for="wcml_editor_save_mode_editor_scoped">
							<strong><?php /* translators: Label of the second radio button of the variation-sync setting. Verb phrase, imperative; "the editor" is the WooCommerce product editor. */ esc_html_e( 'Only sync variations edited in the editor', 'woocommerce-multilingual' ); ?></strong><br/>
							<span style="color:#3c434a;">
								<?php /* translators: Explanation under the second radio button of the variation-sync setting. */ esc_html_e( 'WPML syncs only the variations changed in this save. After each save, WPML shows what was synced and offers a one-click full sync.', 'woocommerce-multilingual' ); ?>
							</span>
						</label>
					</li>
				</ul>
			</div>
		</div>
		<?php
	}
}

