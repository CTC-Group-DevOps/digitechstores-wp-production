/**
 * Modal dialog implementation for editor.
 * Handles events, sizing, messages, and modal integration.
 *
 * @type {*}
 */

( function ( $ ) {
	'use strict';
	if ( _.isUndefined( window.vc ) ) {
		window.vc = {};
	}
	vc.ModalView = Backbone.View.extend({
		message_box_timeout: false,
		events: {
			'hidden.bs.modal': 'hide',
			'shown.bs.modal': 'shown'
		},
		initialize: function () {
			_.bindAll( this, 'setSize', 'hide' );
		},
		setSize: function () {
			var height = $( window ).height() - 150;
			this.$content.css( 'maxHeight', height );
			this.trigger( 'setSize' );
		},
		render: function () {
			$( window ).on( 'resize.ModalView', this.setSize );
			this.setSize();
			vc.closeActivePanel();
			this.$el.modal( 'show' );
			return this;
		},
		hide: function () {
			$( window ).off( 'resize.ModalView' );
		},
		shown: function () {
		}
	});
})( window.jQuery );
