( function ( $ ) {
	$( document ).on( 'mfpOpen', function () {
		$( '.bundle_form .bundle_data' ).each( function () {
			const $bundle_data = $( this ),
				$composite_form = $bundle_data.closest( '.composite_form' );

			if ( $composite_form.length === 0 ) {
				$bundle_data.wc_pb_bundle_form();
			}
		} );
	} );
} )( jQuery );
