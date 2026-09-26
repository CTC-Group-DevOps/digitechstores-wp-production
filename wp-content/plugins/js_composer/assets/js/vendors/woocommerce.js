if ( !window.ajaxurl ) {
	window.ajaxurl = window.location.href;
}
( function () {
	'use strict';

	var vcWoocommerceProductAttributeFilterDependencyCallback;

	vcWoocommerceProductAttributeFilterDependencyCallback = function () {
		( function ( $, that ) {
			var $filterDropdown, $empty;

			$filterDropdown = $( '[data-vc-shortcode-param-name="filter"]', that.$content );
			$empty = $( '#filter-empty', $filterDropdown );
			if ( $empty.length ) {
				$empty.parent().remove();
				$filterDropdown.addClass( 'vc_dependent-hidden' );
			} else {
				$filterDropdown.removeClass( 'vc_dependent-hidden' );
			}
			$( 'select[name="attribute"]', that.$content ).on( 'change', function () {
				$( '.vc_checkbox-label', $filterDropdown ).remove();

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: window.ajaxurl,
					data: {
						action: 'vc_woocommerce_get_attribute_terms',
						attribute: this.value,
						_vcnonce: window.vcAdminNonce
					}
				}).done( function ( data ) {
					if ( 0 < data.length ) {
						$filterDropdown.removeClass( 'vc_dependent-hidden' );
						$( '.wpb_checkbox-container-vertical', $filterDropdown ).prepend( $( data ) );
					} else {
						$filterDropdown.addClass( 'vc_dependent-hidden' );
					}
				});
			}).trigger( 'change' );
		}( window.jQuery, this ) );
	};

	window.vcWoocommerceProductAttributeFilterDependencyCallback = vcWoocommerceProductAttributeFilterDependencyCallback;
})();
