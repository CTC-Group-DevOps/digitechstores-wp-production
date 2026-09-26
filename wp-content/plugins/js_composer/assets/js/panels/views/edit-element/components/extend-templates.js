/**
 * vc.ExtendTemplates object, provides functionality for managing
 * element templates in the editor. It includes methods for saving, loading, and
 * displaying templates, as well as handling UI interactions such as dialogs for template management.
 * The file integrates AJAX calls to interact with the backend for template-related operations.
 *
 * vc.ExtendTemplates object is initialized in the window.vc.EditElementPanelView
 */

( function ( $ ) {
	'use strict';

	window.vc.ExtendTemplates = {
		isTemplateViewActive: false,
		fetchSaveTemplateDialogAjaxData: function () {
			return {
				action: 'vc_action_render_settings_templates_prompt',
				vc_inline: true,
				_vcnonce: window.vcAdminNonce
			};
		},
		fetchSaveTemplateDialog: function ( callback ) {
			const $tab = this.$el.find( '.vc_ui-panel-content-container' );

			if ( $tab.find( '.vc_ui-prompt-templates' ).length ) {
				if ( 'undefined' !== typeof ( callback ) ) {
					callback( false );
				}
				return;
			}

			this.checkAjax();
			this.ajax = $.ajax({
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: this.fetchSaveTemplateDialogAjaxData()
			}).done( function ( response ) {
				if ( response.success ) {
					$tab.prepend( response.html );

					if ( 'undefined' !== typeof ( callback ) ) {
						callback( true );
					}
				} else {
					// Response success is false - invoke callback with false
					if ( 'undefined' !== typeof ( callback ) ) {
						callback( false );
					}
				}
			}).fail( ( e ) => {
				console.error( 'Failed to fetch save template dialog.', e );
				// AJAX request failed - invoke callback with false
				if ( 'undefined' !== typeof ( callback ) ) {
					callback( false );
				}
			}).always( this.resetAjax );

			return this.ajax;
		},
		showSaveTemplateDialog () {
			const _this = this;

			this.fetchSaveTemplateDialog( ( created ) => {
				// If created is false and dialog doesn't exist, fetch failed - reset flag
				const $tab = _this.$el.find( '.vc_ui-panel-content-container' );
				const $prompt = $tab.find( '.vc_ui-prompt-templates' );

				// Check if prompt actually exists (fetch was successful)
				if ( !$prompt.length ) {
					// Fetch failed - reset flag and exit
					_this.isTemplateViewActive = false;
					return;
				}

				const $title = $prompt.find( '.textfield' );
				$tab.find( '.vc_ui-prompt.vc_visible' ).removeClass( 'vc_visible' );

				$prompt.addClass( 'vc_visible' );
				$title.trigger( 'focus' );
				$tab.addClass( 'vc_ui-content-hidden' );

				// Set flag to indicate template dialog is active (only after dialog is shown)
				_this.isTemplateViewActive = true;

				// Hide all tabs when entering template view
				_this.$el.find( '.vc_edit-form-tab-control' ).hide();

				// Update resizable minHeight based on panel visibility
				_this.updateResizableMinHeight?.();

				if ( !created ) {
					return;
				}
				let delay = 0;
				const $btn = $prompt.find( '#vc_ui-save-templates-btn' );

				$prompt.on( 'submit', function () {
					const title = $title.val();

					const data = {
						action: vc.templates_panel_view.save_template_action,
						template: vc.shortcodes.singleStringify( _this.model.get( 'id' ), 'template' ),
						template_name: title,
						vc_inline: true,
						_vcnonce: window.vcAdminNonce
					};

					vc.templates_panel_view.reloadTemplateList( data, () => {
						$title.val( '' );
						_this.setCustomButtonMessage( $btn, undefined, undefined, true );

						delay = _.delay( () => {
							// Reset flag when dialog closes
							_this.isTemplateViewActive = false;
							$prompt.removeClass( 'vc_visible' );
							$tab.removeClass( 'vc_ui-content-hidden' );

							// Show tabs when exiting template view
							_this.$el.find( '.vc_edit-form-tab-control' ).show();

							// Update resizable minHeight based on panel visibility
							_this.updateResizableMinHeight?.();
						}, 5000 );
					}, () => {
						_this.setCustomButtonMessage( $btn, window.i18nLocale.ui_danger, 'danger' );
					});

					return false;
				});

				$prompt.on( 'click', '.vc_ui-prompt-close', function () {
					// This handler is for closing the template DIALOG, not the dropdown "Edit element"
					// Reset flag when closing dialog
					_this.isTemplateViewActive = false;
					_this.checkAjax();
					$prompt.removeClass( 'vc_visible' );
					$tab.removeClass( 'vc_ui-content-hidden' );

					// Show tabs when exiting template view
					_this.$el.find( '.vc_edit-form-tab-control' ).show();

					// Update resizable minHeight based on panel visibility
					_this.updateResizableMinHeight?.();

					_this.clearCustomButtonMessage.call( this, $btn );
					if ( delay ) {
						window.clearTimeout( delay );
						delay = 0;
					}
					return false;
				});

				$( '.edit-form-info' ).initializeTooltips();
			});
		}
	};
})( window.jQuery );
