jQuery( document ).ready( function ( $ ) {
	'use strict';

	if ( window.Vc_postSettingsEditor ) {
		function setEditorNewValue ( $editor_input, editor_slug ) {
			// set new value to textarea
			$editor_input.val( window[editor_slug].getValue() );
		}

		const editorJsHeader = new Vc_postSettingsEditor( '', 'settings_editor_js_header' );
		editorJsHeader.sel = 'wpb_js_header_editor';
		editorJsHeader.mode = 'javascript';
		const editorJsFooter = new Vc_postSettingsEditor( '', 'settings_editor_js_footer' );
		editorJsFooter.sel = 'wpb_js_footer_editor';
		editorJsFooter.mode = 'javascript';

		var editor_list = {
			js_header: editorJsHeader,
			js_footer: editorJsFooter
		};

		for ( var editor_name in editor_list ) {
			var $editor = $( '#wpb_' + editor_name + '_editor' );
			if ( $editor.length ) {
				var $editor_input = $editor.prev();
				var editor_slug = 'editor' + editor_name;
				window[editor_slug] = editor_list[editor_name];
				window[editor_slug].setEditor( $editor_input.val() );

				window[editor_slug].getEditor().on( 'change', setEditorNewValue.bind( null, $editor_input, editor_slug ) );
			}
		}
	}
});
