/* global wc_bundles_admin_order_params */
/* global woocommerce_admin_meta_boxes */
jQuery( function ( $ ) {
	const $order_items = $( '#woocommerce-order-items' );
	let view = false;
	const functions = {
		handle_events: function () {
			$order_items

				.on(
					'click',
					'button.configure_bundle',
					{ action: 'configure' },
					this.clicked_edit_button
				)
				.on(
					'click',
					'button.edit_bundle',
					{ action: 'edit' },
					this.clicked_edit_button
				);

			$( document.body ).on(
				'click',
				'input.bundled_product_checkbox',
				this.toggle_optional_item
			);
		},

		// eslint-disable-next-line @typescript-eslint/no-unused-vars -- Keep the existing callback signature in this lint-only change.
		toggle_optional_item: function ( event ) {
			const $input = $( this ),
				is_checked = $input.is( ':checked' ),
				$content = $input
					.closest( '.details' )
					.find( '.bundled_item_cart_content' );

			if ( is_checked ) {
				$content.show();
			} else {
				$content.hide();
			}
		},

		clicked_edit_button: function ( event ) {
			const WCPBBackboneModal = $.WCBackboneModal.View.extend( {
				addButton: functions.clicked_done_button,
			} );

			const $item = $( this ).closest( 'tr.item' ),
				item_id = $item.attr( 'data-order_item_id' );

			view = new WCPBBackboneModal( {
				target: 'wc-modal-edit-bundle',
				string: {
					action:
						event.data.action === 'configure'
							? wc_bundles_admin_order_params.i18n_configure
							: wc_bundles_admin_order_params.i18n_edit,
					item_id,
				},
			} );

			functions.populate_form();

			return false;
		},

		clicked_done_button: function ( event ) {
			view.is_valid = true;
			$order_items.trigger( 'wc_pb_validate_form', view );

			if ( ! view.is_valid ) {
				return false;
			}

			const data = $.extend( {}, functions.get_taxable_address(), {
				action: 'woocommerce_edit_bundle_in_order',
				item_id: view._string.item_id,
				fields: view.$el.find( 'input, select, textarea' ).serialize(),
				dataType: 'json',
				order_id: woocommerce_admin_meta_boxes.post_id,
				security: wc_bundles_admin_order_params.edit_bundle_nonce,
			} );

			const form = view.$el.find( 'form' )[ 0 ];
			const form_data = new FormData( form );
			for ( const property in data ) {
				form_data.append( property, data[ property ] );
			}

			functions.block( view.$el.find( '.wc-backbone-modal-content' ) );

			$.post( {
				url: woocommerce_admin_meta_boxes.ajax_url,
				type: 'POST',
				data: form_data,
				processData: false,
				contentType: false,
				cache: false,
				success: function ( response ) {
					if ( response.result && response.result === 'success' ) {
						$order_items.find( '.inside' ).empty();
						$order_items.find( '.inside' ).append( response.html );

						$order_items.trigger( 'wc_order_items_reloaded' );

						// Update notes.
						if ( response.notes_html ) {
							$( 'ul.order_notes' ).empty();
							$( 'ul.order_notes' ).append(
								$( response.notes_html ).find( 'li' )
							);
						}

						functions.unblock(
							view.$el.find( '.wc-backbone-modal-content' )
						);

						// Make it look like something changed.
						functions.block( $order_items, { fadeIn: 0 } );
						setTimeout( function () {
							functions.unblock( $order_items );
						}, 250 );

						view.closeButton( event );
					} else {
						// eslint-disable-next-line no-alert -- Keep the existing administrator-facing error fallback.
						window.alert(
							$( '<textarea />' )
								.html(
									response.error
										? response.error
										: wc_bundles_admin_order_params.i18n_validation_error
								)
								.text()
						);
						functions.unblock(
							view.$el.find( '.wc-backbone-modal-content' )
						);
					}
				},
				error: function () {
					// eslint-disable-next-line no-alert -- Keep the existing administrator-facing error fallback.
					window.alert(
						wc_bundles_admin_order_params.i18n_validation_error
					);
					functions.unblock(
						view.$el.find( '.wc-backbone-modal-content' )
					);
				},
			} );
		},

		populate_form: function () {
			functions.block( view.$el.find( '.wc-backbone-modal-content' ) );

			const data = {
				action: 'woocommerce_configure_bundle_order_item',
				item_id: view._string.item_id,
				dataType: 'json',
				order_id: woocommerce_admin_meta_boxes.post_id,
				security: wc_bundles_admin_order_params.edit_bundle_nonce,
			};

			$.post(
				woocommerce_admin_meta_boxes.ajax_url,
				data,
				function ( response ) {
					if ( response.result && response.result === 'success' ) {
						view.$el.find( 'form' ).html( response.html );
						functions.unblock(
							view.$el.find( '.wc-backbone-modal-content' )
						);
						$order_items.trigger( 'wc_pb_populate_form', view );
					} else {
						// eslint-disable-next-line no-alert -- Keep the existing administrator-facing error fallback.
						window.alert(
							wc_bundles_admin_order_params.i18n_form_error
						);
						functions.unblock(
							view.$el.find( '.wc-backbone-modal-content' )
						);
						view.$el.find( '.modal-close' ).trigger( 'click' );
					}
				}
			);
		},

		get_taxable_address: function () {
			let country = '';
			let state = '';
			let postcode = '';
			let city = '';

			if ( woocommerce_admin_meta_boxes.tax_based_on === 'shipping' ) {
				country = $( '#_shipping_country' ).val();
				state = $( '#_shipping_state' ).val();
				postcode = $( '#_shipping_postcode' ).val();
				city = $( '#_shipping_city' ).val();
			}

			if (
				woocommerce_admin_meta_boxes.tax_based_on === 'billing' ||
				! country
			) {
				country = $( '#_billing_country' ).val();
				state = $( '#_billing_state' ).val();
				postcode = $( '#_billing_postcode' ).val();
				city = $( '#_billing_city' ).val();
			}

			return {
				country,
				state,
				postcode,
				city,
			};
		},

		block: function ( $target, params ) {
			const defaults = {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			};

			const opts = $.extend( {}, defaults, params || {} );

			$target.block( opts );
		},

		unblock: function ( $target ) {
			$target.unblock();
		},
	};

	/*
	 * Initialize.
	 */
	functions.handle_events();
} );
