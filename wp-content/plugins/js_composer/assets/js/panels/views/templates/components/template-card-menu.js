( function ( $ ) {
	'use strict';

	const toggleClass = '.vc_template-options-toggle';
	const dropdownClass = '.vc_template-options-dropdown';
	const optionClass = '.select2-results__option';
	const initFlag = 'vcTemplateMenuInited';

	let $openDropdown = null;
	let $openToggle = null;

	function getDeleteAction () {
		const view = window.vc && window.vc.templates_panel_view;
		return ( view && view.delete_template_action ) || 'vc_delete_template';
	}

	function showPanelMessage ( text, type ) {
		if ( ! text || ! window.wpbNotifications ) {
			return;
		}
		window.wpbNotifications.show( text, { type });
	}

	function deleteCard ( $card ) {
		const templateId = $card.attr( 'data-template_id' );
		const templateType = $card.attr( 'data-template_type' );
		const templateAction = $card.attr( 'data-template_action' );
		const templateName = $card.find( '[data-vc-ui-element="template-title"]' ).text();
		const i18n = ( window.i18nLocale && window.i18nLocale.confirm_deleting_template ) || 'Delete "{template_name}"?';

		if ( ! templateId ) {
			return;
		}
		if ( ! window.confirm( i18n.replace( '{template_name}', templateName ) ) ) {
			return;
		}

		$.ajax({
			type: 'POST',
			url: window.ajaxurl,
			data: {
				action: templateAction || getDeleteAction(),
				template_id: templateId,
				template_type: templateType,
				vc_inline: true,
				_vcnonce: window.vcAdminNonce
			}
		}).done( () => {
			$card.remove();
			if ( window.vc && window.vc.events ) {
				window.vc.events.trigger( 'templates:delete', {
					id: templateId,
					type: templateType
				});
			}
			showPanelMessage( window.i18nLocale && window.i18nLocale.template_removed, 'success' );
		}).fail( ( jqXHR, textStatus, errorThrown ) => {
			console.error( 'Template delete failed:', textStatus, errorThrown, jqXHR );
			showPanelMessage( ( window.i18nLocale && window.i18nLocale.template_save_error ) || 'Failed to delete template.', 'error' );
		});
	}

	function onDocumentMouseUp ( e ) {
		if ( ! $openDropdown ) {
			return;
		}
		const $target = $( e.target );
		if ( $target.closest( dropdownClass ).length || $target.closest( toggleClass ).length ) {
			return;
		}
		closeDropdown();
	}

	function onDocumentKeyDown ( e ) {
		if ( 'Escape' === e.key && $openDropdown ) {
			closeDropdown();
		}
	}

	function closeDropdown () {
		if ( $openDropdown ) {
			$openDropdown.attr( 'hidden', true );
		}
		if ( $openToggle ) {
			$openToggle.attr( 'aria-expanded', 'false' );
		}
		$openDropdown = null;
		$openToggle = null;

		// Remove global listeners while no dropdown is open.
		$( document ).off( 'mouseup.vcTemplateMenu', onDocumentMouseUp );
		$( document ).off( 'keydown.vcTemplateMenu', onDocumentKeyDown );
	}

	function openDropdown ( $toggle ) {
		closeDropdown();
		const $dropdown = $toggle.siblings( dropdownClass );
		if ( ! $dropdown.length ) {
			return;
		}
		$dropdown.removeAttr( 'hidden' );
		$toggle.attr( 'aria-expanded', 'true' );
		$openDropdown = $dropdown;
		$openToggle = $toggle;

		// Bind global listeners only while a dropdown is open.
		$( document ).on( 'mouseup.vcTemplateMenu', onDocumentMouseUp );
		$( document ).on( 'keydown.vcTemplateMenu', onDocumentKeyDown );
	}

	function handleAction ( $toggle, action ) {
		const $card = $toggle.closest( '[data-template_id]' );
		if ( ! $card.length ) {
			return;
		}

		if ( 'edit' === action ) {
			const url = $toggle.data( 'edit-url' );
			if ( url ) {
				window.open( url, '_blank', 'noopener' );
			}
			return;
		}

		if ( 'delete' === action ) {
			deleteCard( $card );
		}
	}

	function initOne ( toggle ) {
		const $toggle = $( toggle );
		if ( $toggle.data( initFlag ) ) {
			return;
		}
		$toggle.data( initFlag, true );

		$toggle.on( 'click', ( e ) => {
			e.preventDefault();
			e.stopPropagation();
			if ( $openToggle && $openToggle.is( $toggle ) ) {
				closeDropdown();
			} else {
				openDropdown( $toggle );
			}
		});

		// Prevent default click navigation/focus side effects.
		$toggle.on( 'click', ( e ) => {
			e.preventDefault();
			e.stopPropagation();
		});

		// Option selection.
		$toggle.siblings( dropdownClass ).on( 'mouseup', optionClass, ( e ) => {
			e.preventDefault();
			e.stopPropagation();
			const action = $( e.currentTarget ).data( 'action' );
			closeDropdown();
			handleAction( $toggle, action );
		});
	}

	function initAll ( $scope ) {
		const root = $scope && $scope.length ? $scope : $( document );
		root.find( toggleClass ).each( ( _, el ) => initOne( el ) );
	}

	$( () => {
		initAll();
		if ( window.vc && window.vc.events ) {
			window.vc.events.on( 'templates:cardAdded', initAll );
		}
	});

	window.vc = window.vc || {};
	window.vc.initTemplateCardMenus = initAll;
})( window.jQuery );
