/* global woodmart_settings */
(function($) {
	woodmartThemeModule.$document.on('wdHeaderBuilderInited', function () {
		woodmartThemeModule.stickyDetails();
	});

	woodmartThemeModule.$document.on('wc-product-gallery-before-destroy', '.woocommerce-product-gallery', function() {
		$(this).trigger('sticky_kit:detach');
	});

	woodmartThemeModule.stickyDetails = function() {
		woodmartThemeModule.$window.off('resize.wdStickyDetails');

		if (!$('.single-product-page').hasClass('wd-sticky-on') || woodmartThemeModule.$window.width() <= 1024) {
			return;
		}

		var details = $('.entry-summary');

		details.each(function() {
			var $column = $(this),
				offset  = parseInt(woodmart_settings.sticky_product_details_offset),
				$inner  = $column.find('.summary-inner'),
				$images = $column.parent().find('.woocommerce-product-gallery');

			if (!$inner.length || !$images.length) {
				return;
			}

			$inner.trigger('sticky_kit:detach');
			$images.trigger('sticky_kit:detach');

			var diff = $inner.outerHeight() - $images.outerHeight();
			var defaultDiff = parseInt(woodmart_settings.sticky_product_details_different);

			if (diff < -defaultDiff) {
				$inner.stick_in_parent({
					offset_top: offset
				});
			} else if (diff > defaultDiff) {
				$images.stick_in_parent({
					offset_top: offset
				});
			}

			woodmartThemeModule.$window.on('resize.wdStickyDetails', woodmartThemeModule.debounce(function() {
				var $currentInner  = $column.find('.summary-inner'),
					$currentImages = $column.parent().find('.woocommerce-product-gallery');

				if (!$currentInner.length || !$currentImages.length) {
					return;
				}

				if (woodmartThemeModule.$window.width() <= 1024) {
					$currentInner.trigger('sticky_kit:detach');
					$currentImages.trigger('sticky_kit:detach');
				} else if ($currentInner.outerHeight() < $currentImages.outerHeight()) {
					$currentInner.stick_in_parent({
						offset_top: offset
					});
				} else {
					$currentImages.stick_in_parent({
						offset_top: offset
					});
				}
			}, 300));
		});
	};

	$(document).ready(function() {
		woodmartThemeModule.stickyDetails();
	});
})(jQuery);
