/**
 * vc.ExtendPresets object, provides functionality for managing
 * element settings presets. It includes methods for saving, applying, and
 * restoring presets, as well as handling UI interactions such as dropdown menus and dialogs
 * for preset management. The file also integrates AJAX calls to interact with the backend
 * for preset-related operations.
 *
 * vc.ExtendPresets object is initialized in the window.vc.EditElementPanelView
 */

( function ( $ ) {
	'use strict';

	window.vc.ExtendPresets = {
		settingsMenuSelector: '[data-vc-ui-element="settings-dropdown-list"]',
		settingsButtonSelector: '[data-vc-ui-element="settings-dropdown-button"]',
		settingsDropdownSelector: '[data-vc-ui-element="settings-dropdown"]',
		settingsPresetId: null,
		isPresetViewActive: false,
		uiEvents: {
			'init': 'addEvents',
			'render': 'hideDropdown',
			'afterRender': 'afterRenderActions'
		},
		afterRenderActions: function () {
			this.untaintSettingsPresetData();
			this.showDropdown();
		},
		hideDropdown: function () {
			this.$el.find( '[data-vc-ui-element="settings-dropdown"]' ).hide();
			// Reset flags when hiding panel to ensure clean state on next open
			this.isPresetViewActive = false;
			this.isTemplateViewActive = false;
			// Show tabs when hiding dropdown (reset to normal edit mode)
			this.$el.find( '.vc_edit-form-tab-control' ).show();
		},
		showDropdown: function () {
			// must be called when content added to DOM
			const tag = this.model.get( 'shortcode' );
			if ( window.vc_settings_show && 'vc_column' !== tag ) {
				this.$el.find( '[data-vc-ui-element="settings-dropdown"]' ).show();
			}
		},
		showDropdownMenu: function () {
			const tag = this.model.get( 'shortcode' );

			const $this = $( this );

			if ( $this.data( 'vcSettingsMenuLoaded' ) && tag === $this.data( 'vcShortcodeName' ) ) {
				return;
			}

			this.reloadSettingsMenuContent();
		},
		addEvents: function () {
			var $tab = this.$el.find( '.vc_edit-form-tab.vc_active' );
			var tag = this.model.get( 'shortcode' );
			var _this = this;

			$( document ).off( 'beforeMinimize.vc.paramWindow',
				this.minimizeButtonSelector ).on( 'beforeMinimize.vc.paramWindow', this.minimizeButtonSelector,
				function () {
					// Close preset prompt if it's visible
					const $presetPrompt = $tab.find( '.vc_ui-prompt-presets' );
					if ( $presetPrompt.hasClass( 'vc_visible' ) ) {
						$presetPrompt.removeClass( 'vc_visible' );
						$tab.removeClass( 'vc_ui-content-hidden' );
					}

					// Close template prompt if it's visible
					const $templatePrompt = $tab.find( '.vc_ui-prompt-templates' );
					if ( $templatePrompt.hasClass( 'vc_visible' ) ) {
						$templatePrompt.removeClass( 'vc_visible' );
						$tab.removeClass( 'vc_ui-content-hidden' );
					}

					// Always reset flags to ensure clean state
					_this.isPresetViewActive = false;
					_this.isTemplateViewActive = false;

					// Show tabs when exiting preset/template view
					_this.$el.find( '.vc_edit-form-tab-control' ).show();

					// Update resizable minHeight based on panel visibility
					_this.updateResizableMinHeight?.();
				}
			);

			$( document ).off( 'close.vc.paramWindow',
				this.closeButtonSelector ).on( 'beforeClose.vc.paramWindow', this.closeButtonSelector,
				function () {
					// Close preset prompt if it's visible
					const $presetPrompt = $tab.find( '.vc_ui-prompt-presets' );
					if ( $presetPrompt.hasClass( 'vc_visible' ) ) {
						$presetPrompt.removeClass( 'vc_visible' );
						$tab.removeClass( 'vc_ui-content-hidden' );
					}

					// Close template prompt if it's visible
					const $templatePrompt = $tab.find( '.vc_ui-prompt-templates' );
					if ( $templatePrompt.hasClass( 'vc_visible' ) ) {
						$templatePrompt.removeClass( 'vc_visible' );
						$tab.removeClass( 'vc_ui-content-hidden' );
					}

					// Always reset flags to ensure clean state
					_this.isPresetViewActive = false;
					_this.isTemplateViewActive = false;

					// Show tabs when exiting preset/template view
					_this.$el.find( '.vc_edit-form-tab-control' ).show();

					// Update resizable minHeight based on panel visibility
					_this.updateResizableMinHeight?.();
				}
			);

			$( document ).off( 'show.vc.accordion', this.settingsButtonSelector ).on( 'show.vc.accordion',
				this.settingsButtonSelector,
				function () {
					const $this = $( this );

					if ( $this.data( 'vcSettingsMenuLoaded' ) && tag === $this.data( 'vcShortcodeName' ) ) {
						// Menu already loaded, just update the disabled states
						_this.updateDropdownItemStates();
						// Don't return - let the accordion event continue so dropdown can show
					} else {
						_this.reloadSettingsMenuContent();

						// Update disabled states based on current mode
						_this.updateDropdownItemStates();
					}
				}
			);

			// Prevent clicks on dropdown menu from closing it unless it's an action item
			this.$el.on( 'click', this.settingsMenuSelector, ( e ) => {
				const $target = $( e.target );
				const $dropdownItem = $target.closest( '.vc_ui-dropdown-item' );

				// If not clicking on a dropdown item (empty space, padding, etc), stop propagation
				if ( !$dropdownItem.length ) {
					e.stopPropagation();
					return;
				}

				// If clicking on a dropdown item, check if it's disabled
				if ( $dropdownItem.hasClass( 'select2-results__option--disabled' ) ) {
					// Item is disabled - already handled in the specific item handlers
					// Just let it bubble (will be caught by those handlers)
					return;
				}

				// Clicking on an enabled action item - let it propagate and close dropdown
				// (the item's own handler will do its action first, then closeSettings() will be called)
			});
		},
		/**
		 * Update dropdown item states based on current mode
		 * - When preset/template view is active: disable save actions, enable edit element
		 * - When edit form is active: disable edit element, enable save actions
		 */
		updateDropdownItemStates () {
			const $menu = this.$el.find( this.settingsMenuSelector );
			const $editElement = $menu.find( '.vc_ui-prompt-close' );
			const $saveTemplate = $menu.find( '[data-vc-save-template]' );
			const $savePreset = $menu.find( '[data-vc-save-settings-preset]' );
			const isAnyViewActive = this.isPresetViewActive || this.isTemplateViewActive;

			if ( isAnyViewActive ) {
				// Preset/template view is active - disable save actions, enable edit element
				$editElement.removeClass( 'select2-results__option--disabled' );
				$saveTemplate.addClass( 'select2-results__option--disabled' );
				$savePreset.addClass( 'select2-results__option--disabled' );
			} else {
				// Edit form is active - disable edit element, enable save actions
				$editElement.addClass( 'select2-results__option--disabled' );
				$saveTemplate.removeClass( 'select2-results__option--disabled' );
				$savePreset.removeClass( 'select2-results__option--disabled' );
			}
		},
		saveSettingsAjaxData: function ( shortcodeName, title, isDefault, data ) {
			return {
				action: 'vc_action_save_settings_preset',
				shortcode_name: shortcodeName,
				is_default: isDefault ? 1 : 0,
				vc_inline: true,
				title: title,
				data: data,
				_vcnonce: window.vcAdminNonce
			};
		},
		saveSettings: function ( title, isDefault ) {
			const shortcodeName = this.model.get( 'shortcode' ),
				paramsForPreset = this.cachedParamsForPreset || this.getParamsForSettingsPreset(),
				data = JSON.stringify( paramsForPreset );

			this.cachedParamsForPreset = null;

			if ( 'undefined' === typeof ( isDefault ) ) {
				isDefault = false;
			}

			this.checkAjax();
			this.ajax = $.ajax({
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: this.saveSettingsAjaxData( shortcodeName, title, isDefault, data ),
				context: this
			}).done( function ( response ) {
				if ( response.success ) {
					this.setSettingsMenuContent( response.html );
					this.settingsPresetId = response.id;
					this.untaintSettingsPresetData();
				}
			}).fail( ( e ) => {
				console.error( 'Failed to save settings preset.', e );
			}).always( this.resetAjax );

			return this.ajax;
		},
		fetchSaveSettingsDialogAjaxData: function () {
			return {
				action: 'vc_action_render_settings_preset_title_prompt',
				vc_inline: true,
				_vcnonce: window.vcAdminNonce
			};
		},
		/**
		 * Fetch save settings dialog and insert it into DOM
		 *
		 * First param of callback function will be passed bool value whether dialog was created (true) or already existed in DOM (false)
		 *
		 * @param {function} callback function to execute after element has been added to DOM
		 */
		fetchSaveSettingsDialog: function ( callback ) {
			const $contentContainer = this.$el.find( '.vc_ui-panel-content-container' );

			if ( $contentContainer.find( '.vc_ui-prompt-presets' ).length ) {
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
				data: this.fetchSaveSettingsDialogAjaxData()
			}).done( function ( response ) {
				if ( response.success ) {

					$contentContainer.prepend( response.html );

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
				console.error( 'Failed to fetch save settings dialog.', e );
				if ( 'undefined' !== typeof ( callback ) ) {
					callback( false );
				}
			}).always( this.resetAjax );
		},
		showSaveSettingsDialog: function ( isDefault ) {
			const _this = this;

			this.isSettingsPresetDefault = !!isDefault;
			this.cachedParamsForPreset = this.getParamsForSettingsPreset();

			this.fetchSaveSettingsDialog( function ( created ) {
				const $contentContainer = _this.$el.find( '.vc_ui-panel-content-container' );
				const $prompt = $contentContainer.find( '.vc_ui-prompt-presets' );

				// Check if prompt actually exists (fetch was successful)
				if ( !$prompt.length ) {
					// Fetch failed - reset flag and exit
					_this.isPresetViewActive = false;
					return;
				}

				const $title = $prompt.find( '.textfield' );
				$contentContainer.find( '.vc_ui-prompt.vc_visible' ).removeClass( 'vc_visible' );

				$prompt.addClass( 'vc_visible' );
				$title.trigger( 'focus' );
				$contentContainer.addClass( 'vc_ui-content-hidden' );

				// Set flag to indicate preset dialog is active (only after dialog is shown)
				_this.isPresetViewActive = true;

				// Hide all tabs when entering preset view
				_this.$el.find( '.vc_edit-form-tab-control' ).hide();

				// Update resizable minHeight based on panel visibility
				_this.updateResizableMinHeight?.();

				_this.resetMinimize();

				if ( !created ) {
					return;
				}
				const $btn = $prompt.find( '#vc_ui-save-preset-btn' );
				let delay = 0;
				_this.updateDropdownItemStates();
				$prompt.on( 'submit', function () {
					const title = $title.val();

					_this.saveSettings( title, _this.isSettingsPresetDefault ).done( function ( e ) {
						const data = this.getParamsForSettingsPreset();
						$title.val( '' );
						_this.setCustomButtonMessage( $btn, undefined, undefined, true );
						const savedTitle = e.title || title;
						vc.events.trigger( 'vc:savePreset', e.id, _this.model.get( 'shortcode' ), savedTitle, data );
						delay = _.delay( function () {
							// Reset flag when dialog closes
							_this.isPresetViewActive = false;
							$prompt.removeClass( 'vc_visible' );
							$contentContainer.removeClass( 'vc_ui-content-hidden' );

							// Show tabs when exiting preset view
							_this.$el.find( '.vc_edit-form-tab-control' ).show();

							// Update resizable minHeight based on panel visibility
							_this.updateResizableMinHeight?.();
						}, 5000 );
					}).fail( ( e ) => {
						console.error( 'Failed to save settings preset.', e );
						_this.setCustomButtonMessage( $btn, window.i18nLocale.ui_danger, 'danger', true );
					});

					return false;
				});

				$prompt.on( 'click', '.vc_ui-prompt-close', function () {
					// Reset flag when closing dialog
					_this.isPresetViewActive = false;
					_this.checkAjax();
					$prompt.removeClass( 'vc_visible' );
					$contentContainer.removeClass( 'vc_ui-content-hidden' );

					// Show tabs when exiting preset view
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
		},
		saveAsDefaultSettingsAjaxData: function ( shortcodeName, id ) {
			return {
				action: 'vc_action_set_as_default_settings_preset',
				shortcode_name: shortcodeName,
				id: id,
				vc_inline: true,
				_vcnonce: window.vcAdminNonce
			};
		},
		/**
		 * Save currently loaded preset as default
		 *
		 * If no preset has been loaded or loaded preset has been changed (tainted),
		 * show "save as" dialog. Otherwise save w/o any prompt.
		 */
		saveAsDefaultSettings: function ( id, doneCallback ) {
			const shortcodeName = this.model.get( 'shortcode' );
			const presetId = id ? id : this.settingsPresetId;
			// if user has not loaded preset or made any changes...
			if ( !presetId ) {
				this.showSaveSettingsDialog( true );
			} else {
				this.checkAjax();
				this.ajax = $.ajax({
					type: 'POST',
					dataType: 'json',
					url: window.ajaxurl,
					data: this.saveAsDefaultSettingsAjaxData( shortcodeName, presetId ),
					context: this
				}).done( function ( response ) {
					if ( response.success ) {
						this.setSettingsMenuContent( response.html );
						this.untaintSettingsPresetData();
						if ( doneCallback ) {
							doneCallback();
						}
					}
				}).fail( ( e ) => {
					console.error( 'Failed to set preset as default.', e );
				}).always( this.resetAjax );
			}
		},
		restoreDefaultSettingsAjaxData: function ( shortcodeName ) {
			return {
				action: 'vc_action_restore_default_settings_preset',
				shortcode_name: shortcodeName,
				vc_inline: true,
				_vcnonce: window.vcAdminNonce
			};
		},
		/**
		 * Remove "default" flag from currently default preset
		 */
		restoreDefaultSettings: function () {
			const shortcodeName = this.model.get( 'shortcode' );

			this.checkAjax();
			this.ajax = $.ajax({
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: this.restoreDefaultSettingsAjaxData( shortcodeName ),
				context: this
			}).done( function ( response ) {
				if ( response.success ) {
					this.setSettingsMenuContent( response.html );
				}
			}).fail( ( e ) => {
				console.error( 'Failed to restore default settings.', e );
			}).always( this.resetAjax );

		},
		/**
		 * Update settings menu (popup) content with specified html
		 *
		 * @param {string} html
		 */
		setSettingsMenuContent: function ( html ) {
			const $button = this.$el.find( this.settingsButtonSelector ),
				$menu = this.$el.find( this.settingsMenuSelector ),
				shortcodeName = this.model.get( 'shortcode' ),
				_this = this;

			$button.data( 'vcShortcodeName', shortcodeName );
			$menu.html( html );

			$menu.find( '[data-vc-save-settings-preset]' ).on( 'click', function ( e ) {
				if ( $( this ).hasClass( 'select2-results__option--disabled' ) ) {
					e.preventDefault();
					e.stopPropagation();
					return false;
				}

				_this.showSaveSettingsDialog();
				_this.closeSettings();
			});

			$menu.find( '[data-vc-save-template]' ).on( 'click', function ( e ) {
				if ( $( this ).hasClass( 'select2-results__option--disabled' ) ) {
					e.preventDefault();
					e.stopPropagation();
					return false;
				}

				_this.showSaveTemplateDialog();
				_this.closeSettings();
			});

			$menu.find( '[data-vc-save-default-settings-preset]' ).on( 'click', function () {
				_this.saveAsDefaultSettings();
				_this.closeSettings();
			});

			$menu.find( '[data-vc-restore-default-settings-preset]' ).on( 'click', function () {
				_this.restoreDefaultSettings();
				_this.closeSettings();
			});

			$menu.find( '.vc_ui-prompt-close' ).on( 'click', function ( e ) {
				if ( $( this ).hasClass( 'select2-results__option--disabled' ) ) {
					e.preventDefault();
					e.stopPropagation();
					return false;
				}

				if ( _this.isPresetViewActive || _this.isTemplateViewActive ) {
					const $contentContainer = _this.$el.find( '.vc_ui-panel-content-container' );
					$contentContainer.find( '.vc_ui-prompt.vc_visible' ).removeClass( 'vc_visible' );
					$contentContainer.removeClass( 'vc_ui-content-hidden' );

					// Show tabs when exiting preset/template view
					_this.$el.find( '.vc_edit-form-tab-control' ).show();

					// Reset both flags
					_this.isPresetViewActive = false;
					_this.isTemplateViewActive = false;

					// Update resizable minHeight based on panel visibility
					_this.updateResizableMinHeight?.();
					_this.resetMinimize();
				}
				// Always close the dropdown when clicking Edit element
				_this.closeSettings();
			});

		},
		reloadSettingsMenuContentAjaxData: function ( shortcodeName ) {
			return {
				action: 'vc_action_render_settings_preset_popup',
				shortcode_name: shortcodeName,
				vc_inline: true,
				_vcnonce: window.vcAdminNonce
			};
		},
		/**
		 * Reload settings menu (popup) content
		 *
		 * This is envoked for the first time menu is opened and every time preset is
		 * saved or deleted
		 */
		reloadSettingsMenuContent: function () {
			const shortcodeName = this.model.get( 'shortcode' ),
				$button = this.$el.find( this.settingsButtonSelector );
			let success = false;

			this.setSettingsMenuContent( '' );

			this.checkAjax();
			this.ajax = $.ajax({
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: this.reloadSettingsMenuContentAjaxData( shortcodeName ),
				context: this
			}).done( function ( response ) {
				if ( response.success ) {
					success = true;
					this.setSettingsMenuContent( response.html );
					$button
						.data( 'vcSettingsMenuLoaded', true );
				}
			}).fail( ( e ) => {
				console.error( 'Failed to load settings menu content.', e );
			}).always( function () {
				if ( !success ) {
					this.closeSettings();
				}
				this.resetAjax();
			});

			return this.ajax;
		},
		/**
		 * Close settings menu
		 *
		 * @param {boolean} [destroy=false] If true, mark menu as 'not loaded', so next time user opens it, it will be fetched again
		 */
		closeSettings: function ( destroy ) {
			if ( 'undefined' === typeof ( destroy ) ) {
				destroy = false;
			}

			const $menu = this.$el.find( this.settingsMenuSelector ),
				$button = this.$el.find( this.settingsButtonSelector );

			if ( destroy ) {
				$button.data( 'vcSettingsMenuLoaded', false );
				$menu.html( '' );
			}

			$button.vcAccordion( 'hide' );
		},
		/**
		 * Check if setting preset data is tainted in current window
		 *
		 * Every time this.getParamsForSettingsPreset() is accessed and design options are used, new random
		 * classname (vc_custom_RANDOM-DIGITS) is created which would generate different
		 * hash every time, so we delete this random part.
		 *
		 * @return {boolean}
		 */
		isSettingsPresetDataTainted: function () {
			let params = JSON.stringify( this.getParamsForSettingsPreset() );
			params = params.replace( /vc_custom_\d+/, '' );

			return this.$el.data( 'vcSettingsPresetHash' ) !== vc_globalHashCode( params );
		},
		/**
		 * Untaint settings preset data in current window
		 *
		 * @see isSettingsPresetDataTainted for reason why vc_custom_* is removed before hashing
		 */
		untaintSettingsPresetData: function () {
			let params = JSON.stringify( this.getParamsForSettingsPreset() );
			params = params.replace( /vc_custom_\d+/, '' );

			this.$el.data( 'vcSettingsPresetHash', vc_globalHashCode( params ) );
		},
		applySettingsPresetAjaxData: function ( params ) {
			const parentId = this.model.get( 'parent_id' );

			return {
				action: 'vc_edit_form',
				tag: this.model.get( 'shortcode' ),
				parent_tag: parentId ? vc.shortcodes.get( parentId ).get( 'shortcode' ) : null,
				post_id: window.vc_post_id,
				params: params,
				_vcnonce: window.vcAdminNonce
			};
		},
		/**
		 * Render preset
		 *
		 * @see render
		 *
		 * @param {object} params
		 * @return {vc.EditElementPanelView}
		 */
		applySettingsPreset: function ( params ) {
			this.currentModelParams = params;
			vc.events.trigger( 'presets:apply', this.model, params );

			this._killEditor();
			this.trigger( 'render' );
			this.show();

			this.checkAjax();
			this.ajax = $.ajax({
				type: 'POST',
				url: window.ajaxurl,
				data: this.applySettingsPresetAjaxData( params ),
				context: this
			}).done( this.buildParamsContent )
				.fail( ( e ) => {
					console.error( 'Failed to apply settings preset.', e );
				})
				.always( this.resetAjax );

			return this;
		},
		/**
		 * Same as getParams, but exclude some attributes
		 */
		getParamsForSettingsPreset: function () {
			const shortcode = this.model.get( 'shortcode' ),
				params = this.getParams();

			if ( 'vc_column' === shortcode || 'vc_column_inner' === shortcode ) {
				delete params.width;
				delete params.offset;
			}

			return params;
		}
	};

	vc.events.on( 'presets.apply', function ( model, params ) {
		if ( 'vc_tta_section' === model.get( 'shortcode' ) && 'undefined' !== typeof ( params.tab_id ) ) {
			params.tab_id = vc_guid() + '-cl';
		}

		return params;
	});
})( window.jQuery );
