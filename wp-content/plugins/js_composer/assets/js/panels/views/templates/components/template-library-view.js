( function ( $ ) {
	'use strict';

	const templatesFeedUrl = 'https://vc-cc-templates.wpbakery.com/templates.json';
	const storageKey = 'templates';
	const storagePrefix = 'vc4-';
	const storageTtlSeconds = 24 * 60 * 60; // 1 day.
	const fallbackDownloadError = 'Download failed.';
	const fallbackLoadError = 'Failed to load template library.';

	function compareVersions ( a, b ) {
		const partsA = String( a || '' ).split( '.' ).map( n => parseInt( n, 10 ) || 0 );
		const partsB = String( b || '' ).split( '.' ).map( n => parseInt( n, 10 ) || 0 );
		const len = Math.max( partsA.length, partsB.length );
		for ( let i = 0; i < len; i++ ) {
			const x = partsA[ i ] || 0;
			const y = partsB[ i ] || 0;
			if ( x !== y ) {
				return x - y;
			}
		}
		return 0;
	}

	window.vc.TemplateLibraryView = vc.PanelView
		.vcExtendUI( vc.HelperAjax )
		.extend({
			myTemplates: [],
			data: false,
			loaded: false,
			loading: false,
			licenseActive: false,
			$loadingPage: false,
			$downloadPage: false,
			$librarySection: false,
			$libraryGrid: false,
			$libraryEmpty: false,
			$downloadedSection: false,
			$downloadedGrid: false,
			$downloadedEmpty: false,
			compiledLibraryCard: false,
			compiledDownloadedCard: false,
			events: {
				'click [data-dismiss=panel]': 'hide',
				'click .vc_ui-template-card-download-btn': 'downloadButton',
				'click .vc_ui-template-card-update-btn': 'updateButton'
			},

			initialize () {
				_.bindAll( this,
					'getLibrary',
					'renderLibrary',
					'renderDownloaded',
					'deleteTemplate',
					'onTabActivate'
				);

				this.licenseActive = !! ( window.vcTemplatesLibraryData && window.vcTemplatesLibraryData.licenseActive );

				this.$loadingPage = this.$el.find( '.vc_ui-panel-loading' );
				this.$downloadPage = this.$el.find( '.vc_ui-panel-download' );
				this.$librarySection = this.$el.find( '[data-section="library"]' );
				this.$libraryGrid = this.$el.find( '#vc_template-library-grid' );
				this.$libraryEmpty = this.$el.find( '[data-vc-grid-empty="library"]' );
				this.$downloadedSection = this.$el.find( '[data-section="downloaded"]' );
				this.$downloadedGrid = this.$el.find( '#vc_template-library-downloaded' );
				this.$downloadedEmpty = this.$el.find( '[data-vc-grid-empty="downloaded"]' );

				const libraryHtml = $( '#vc_template-library-card' ).html();
				if ( libraryHtml ) {
					this.compiledLibraryCard = vc.template( libraryHtml );
				}
				const downloadedHtml = $( '#vc_template-downloaded-card' ).html();
				if ( downloadedHtml ) {
					this.compiledDownloadedCard = vc.template( downloadedHtml );
				}

				window.vc.events.on( 'templates:delete', this.deleteTemplate );

				$( document ).on( 'click.vcTemplateLibraryView', '[data-vc-ui-element="panel-tab-control"]', this.onTabActivate );
				// Eager fetch so cross-tab search has data before the Template library tab is opened.
				this.getLibrary();
			},

			onTabActivate ( e ) {
				const $btn = $( e.currentTarget );
				const targetSelector = $btn.data( 'vc-ui-element-target' );
				if ( targetSelector && this.$el.is( targetSelector ) ) {
					this.getLibrary();
				}
			},

			getLibrary () {
				if ( this.loaded || this.loading ) {
					this.renderLibrary();
					if ( this.loaded ) {
						this.renderDownloaded();
					}
					return;
				}

				const cached = this.getStorage( storageKey );
				if ( cached && 'object' === typeof cached && ! _.isEmpty( cached ) ) {
					this.loaded = true;
					this.data = cached;
					this.renderLibrary();
					this.renderDownloaded();
					return;
				}

				this.loading = true;
				this.$loadingPage.removeClass( 'vc_ui-hidden' );
				this.checkAjax();
				this.ajax = $.getJSON( templatesFeedUrl )
					.done( data => {
						this.setStorage( storageKey, data );
						this.loaded = true;
						this.data = data;
						this.renderLibrary();
						this.renderDownloaded();
					})
					.fail( () => {
						this.showError( ( window.i18nLocale && window.i18nLocale.ui_templates_failed_to_download ) || fallbackLoadError );
					})
					.always( () => {
						this.loading = false;
						this.$loadingPage.addClass( 'vc_ui-hidden' );
						this.resetAjax();
					});
			},

			renderLibrary () {
				if ( ! this.compiledLibraryCard || ! this.data ) {
					return;
				}

				const locked = ! this.licenseActive;
				const { myTemplates } = this;
				const compile = this.compiledLibraryCard;
				// Already-downloaded items show in the Downloaded section; library only shows
				// items not yet downloaded, or downloaded with a newer version available.
				const visible = this.data.filter( item => {
					const local = _.find( myTemplates, { id: item.id });
					if ( ! local ) {
						return true;
					}
					return compareVersions( local.version, item.version ) < 0;
				});
				const html = visible.map( item => {
					const local = _.find( myTemplates, { id: item.id });
					const downloaded = !! local;
					const updateAvailable = downloaded && compareVersions( local.version, item.version ) < 0;
					return compile({
						id: item.id,
						title: item.title,
						thumbnailUrl: item.thumbnailUrl,
						previewUrl: item.previewUrl,
						version: item.version,
						downloaded,
						updateAvailable,
						locked
					});
				}).join( '' );

				this.$libraryGrid.html( html );
				this.$libraryEmpty.toggleClass( 'vc_ui-hidden', visible.length > 0 );
				this.reapplySearch();
			},

			renderDownloaded () {
				if ( ! this.compiledDownloadedCard || ! this.$downloadedGrid.length ) {
					return;
				}
				// Thumbnails live on the cloud catalog; merge them in by id when available.
				const library = this.data || [];
				const compile = this.compiledDownloadedCard;
				const html = this.myTemplates.map( item => {
					const remote = _.find( library, { id: item.id });
					return compile({
						post_id: item.post_id,
						title: item.title,
						thumbnailUrl: ( remote && remote.thumbnailUrl ) || ''
					});
				}).join( '' );
				this.$downloadedGrid.html( html );

				if ( this.$downloadedEmpty.length ) {
					this.$downloadedEmpty.toggleClass( 'vc_ui-hidden', this.myTemplates.length > 0 );
				}
				this.reapplySearch();
			},

			reapplySearch () {
				const panelView = window.vc && window.vc.templates_panel_view;
				if ( ! panelView || 'function' !== typeof panelView.searchByName ) {
					return;
				}
				const $panel = panelView.$el;
				if ( ! $panel || ! $panel.is( '[data-vc-template-search="true"]' ) ) {
					return;
				}
				const $search = $panel.find( '[data-vc-templates-name-filter]' ).first();
				const value = $search.val();
				if ( value ) {
					panelView.searchByName( value );
				}
			},

			showError ( message ) {
				if ( message ) {
					window.wpbNotifications.show( message, { type: 'error' });
				}
			},

			showDownloadOverlay () {
				this.$downloadPage.removeClass( 'vc_ui-hidden' );
			},

			hideDownloadOverlay ( errorMessage ) {
				this.$downloadPage.addClass( 'vc_ui-hidden' );
				this.showError( errorMessage || '' );
			},

			downloadButton ( e ) {
				if ( e && e.preventDefault ) {
					e.preventDefault();
				}
				const $card = $( e.currentTarget ).closest( '[data-template-id]' );
				const id = $card.data( 'templateId' );
				if ( id ) {
					this.downloadTemplate( id );
				}
			},

			updateButton ( e ) {
				this.downloadButton( e );
			},

			downloadTemplate ( id ) {
				this.checkAjax();
				this.showDownloadOverlay();
				let fail = true;
				this.ajax = $.ajax({
					type: 'POST',
					url: window.ajaxurl,
					data: {
						action: 'vc_shared_templates_download',
						id,
						_vcnonce: window.vcAdminNonce
					},
					dataType: 'json',
					context: this
				}).done( function ( response ) {
					if ( response && response.success ) {
						const template = _.find( this.data, { id });
						if ( template ) {
							fail = false;
							template.post_id = response.data.post_id;
							const existingIndex = _.findIndex( this.myTemplates, { id: template.id });
							if ( existingIndex !== -1 ) {
								this.myTemplates[ existingIndex ] = template;
							} else {
								this.myTemplates.unshift( template );
							}
							this.renderDownloaded();
							this.renderLibrary();
							window.wpbNotifications.show( window.i18nLocale && window.i18nLocale.ui_template_downloaded || 'Template downloaded successfully.' );
						}
					}
				}).always( function ( response, status ) {
					let message = '';
					if ( 'success' !== status || fail ) {
						const localizedMessage = window.i18nLocale && window.i18nLocale.ui_templates_failed_to_download;
						message = ( response && response.data && response.data.message ) || localizedMessage || fallbackDownloadError;
					}
					this.hideDownloadOverlay( message );
					this.resetAjax();
					// Re-run search after the overlay is gone; during render the tab body is
					// hidden so a search-by-name would see zero visible cards.
					this.reapplySearch();
				});
			},

			deleteTemplate ( data ) {
				if ( 'shared_templates' !== data.type ) {
					return;
				}
				const index = _.findIndex( this.myTemplates, { post_id: data.id });
				if ( index === -1 ) {
					return;
				}
				this.myTemplates.splice( index, 1 );
				this.renderDownloaded();
				if ( this.loaded ) {
					this.renderLibrary();
				}
			},

			removeStorage ( name ) {
				try {
					localStorage.removeItem( storagePrefix + name );
					localStorage.removeItem( `${ storagePrefix }${ name }_expiresIn` );
				} catch {
					return false;
				}
				return true;
			},

			getStorage ( key ) {
				const now = Date.now();
				let expiresIn = localStorage.getItem( `${ storagePrefix }${ key }_expiresIn` );
				if ( undefined === expiresIn || null === expiresIn ) {
					expiresIn = 0;
				}
				if ( expiresIn < now ) {
					this.removeStorage( key );
					return null;
				}
				try {
					return JSON.parse( localStorage.getItem( storagePrefix + key ) );
				} catch {
					return null;
				}
			},

			setStorage ( key, value, expires ) {
				const ttlSeconds = ( undefined === expires || null === expires ) ? storageTtlSeconds : Math.abs( expires );
				const schedule = Date.now() + ttlSeconds * 1000;
				try {
					localStorage.setItem( storagePrefix + key, JSON.stringify( value ) );
					localStorage.setItem( `${ storagePrefix }${ key }_expiresIn`, schedule );
				} catch {
					return false;
				}
				return true;
			}
		});

	$( () => {
		if ( ! window.vcTemplatesLibraryData ) {
			return;
		}
		window.vc.templatesLibrary = new vc.TemplateLibraryView({
			el: '[data-vc-ui-element="panel-edit-element-tab"][data-tab="shared_templates"]'
		});
		window.vc.templatesLibrary.myTemplates = window.vcTemplatesLibraryData.templates || [];
		window.vc.templatesLibrary.renderDownloaded();
	});
})( window.jQuery );
