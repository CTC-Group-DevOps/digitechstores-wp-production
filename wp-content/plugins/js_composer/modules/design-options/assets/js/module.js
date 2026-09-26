jQuery( document ).ready( function ( $ ) {

	var isOptionsEnabled = $( '#wpb_js_use_custom' ).prop( 'checked' );
	var pickers = [];
	var pickrOptions = {
		disabled: !isOptionsEnabled
	};
	vc.formComponents.colorPicker.init( null, pickrOptions, null, pickers );

	$( '#vc_settings-color-restore-default' ).on( 'click', function ( e ) {
		e.preventDefault();
		if ( confirm( window.i18nLocaleSettings.are_you_sure_reset_color ) ) {
			$( '#vc_settings-color-action' ).val( 'restore_color' );
			$( '#vc_settings-color' ).attr( 'action', window.location.href ).find( '[type=submit]' ).click();
		}
	});
	$( '#wpb_js_use_custom' ).on( 'change', function () {
		if ( this.checked ) {
			$( '#vc_settings-color' ).addClass( 'color_enabled' );
			pickers.forEach( function ( pickr ) {
				pickr.enable();
			});
		} else {
			$( '#vc_settings-color' ).removeClass( 'color_enabled' );
			pickers.forEach( function ( pickr ) {
				pickr.disable();
			});
		}
	});

	var lessBuilding = false;
	$( '#vc_settings-color' ).on( 'submit', function ( e ) {
		e.preventDefault();
		if ( lessBuilding ) {
			return;
		}
		var form, $submitButton, $designCheckBox;

		form = this;
		$submitButton = $( '#submit_btn' );
		$designCheckBox = $( '#wpb_js_use_custom' );
		if ( $designCheckBox.prop( 'checked' ) && 'restore_color' !== $( '#vc_settings-color-action' ).val() ) {
			lessBuilding = true;
			const modifyVars = $( form ).serializeArray();
			const variablesDataLinker = $submitButton.data( 'vc-less-variables' );
			const $spinner = $submitButton.find( '.vc_settings-save-spinner' );
			const $label = $submitButton.find( '.vc_settings-save-label' );
			$label.text( window.i18nLocaleSettings.loading );
			$spinner.show();

			_.delay( function () {
				vc.less.build({
					modifyVars: modifyVars,
					variablesDataLinker: variablesDataLinker,
					lessPath: $submitButton.data( 'vc-less-path' ),
					rootpath: $submitButton.data( 'vc-less-root' )
				}, function ( output, error ) {
					if ( !_.isUndefined( output ) && !_.isUndefined( output.css ) ) {
						$( '[name="wpb_js_compiled_js_composer_less"]' ).val( output.css );
						var $form = $( '#vc_settings-color' );
						$.ajax({
							type: 'POST',
							url: $form.attr( 'action' ),
							data: $form.eq( 0 ).serializeArray(),
							success: function () {
								window.wpbNotifications.show( window.i18nLocaleSettings.saved, { timeout: 5000 });
								$label.text( window.i18nLocaleSettings.save );
								lessBuilding = false;
								$spinner.hide();
							},
							error: function () {
								window.wpbNotifications.show( window.i18nLocaleSettings.form_save_error, { type: 'error' });
								$label.text( window.i18nLocaleSettings.save );
								lessBuilding = false;
								$spinner.hide();
							}
						});

					} else if ( !_.isUndefined( error ) ) {
						if ( window.console && window.console.warn ) {
							window.console.warn( 'build error', error );
						}
						window.wpbNotifications.show( `${ window.i18nLocaleSettings.save_error }. ${ error }`, { type: 'error' });
						$label.text( window.i18nLocaleSettings.save );
						lessBuilding = false;
						$spinner.hide();
					}
				});
			}, 100 );
		} else {
			form.submit();
		}
	});
});
