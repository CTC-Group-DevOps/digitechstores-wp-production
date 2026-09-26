/**
 * RowLayoutEditorPanelViewBackend extends the row layout editor for backend operations.
 *
 * It customizes layout handling and builder integration for the backend editor.
 */

( function ( $ ) {
	'use strict';
	if ( _.isUndefined( window.vc ) ) {
		window.vc = {};
	}

	var events = {
		'click [data-vc-ui-element="button-save"]': 'save',
		'click [data-vc-ui-element="button-close"]': 'hide',
		'touchstart [data-vc-ui-element="button-close"]': 'hide',
		'click [data-vc-ui-element="button-panel-minimize"]': 'toggleOpacity',
		'click input[name="vc_row_layout_preset"]': 'onPresetChange',
		'focusout #vc_row-layout': 'updateFromInput',
		'keyup #vc_row-layout': 'onInputKeyup'
	};

	vc.RowLayoutEditorPanelViewBackend = vc.RowLayoutEditorPanelView.extend({
		builder () {
			if ( !this.builder ) {
				this.builder = vc.storage;
			}
			return this.builder;
		},
		isBuildComplete () {
			return true;
		},
		onPresetChange ( e ) {
			const value = $( e.currentTarget ).val();
			const columns = this.model.view.convertRowColumns( value );
			this.$input.val( columns.join( ' + ' ) );
		}
	});

	vc.RowLayoutUIPanelBackendEditor = vc.RowLayoutEditorPanelViewBackend
		.vcExtendUI( vc.HelperPanelViewHeaderFooter )
		.vcExtendUI( vc.HelperPanelViewDraggable )
		.extend({
			panelName: 'rowLayouts',
			events: events
		});

})( window.jQuery );
