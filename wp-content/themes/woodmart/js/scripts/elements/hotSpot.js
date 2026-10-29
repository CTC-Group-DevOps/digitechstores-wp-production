/* global woodmart_settings */
(function($) {
	$.each([
		'frontend/element_ready/wd_image_hotspot.default',
	], function(index, value) {
		woodmartThemeModule.wdElementorAddAction(value, function() {
			woodmartThemeModule.imageHotspot();
		});
	});

	woodmartThemeModule.$document.on('wdShopPageInit', function() {
		woodmartThemeModule.imageHotspot();
	});

	woodmartThemeModule.imageHotspot = function() {
		$('.wd-image-hotspot, .wd-spot').each(function() {
			var _this = $(this);
			var btn = _this.find('.hotspot-btn, .wd-spot-icon');
			var parentWrapper = _this.parents('.wd-spots');

			if (!parentWrapper.hasClass('wd-event-click') && woodmartThemeModule.$window.width() > 1024) {
				return;
			}

			btn.on('click', function() {
				if (_this.hasClass('wd-opened')) {
					_this.removeClass('wd-opened');
				} else {
					_this.addClass('wd-opened');
					_this.siblings().removeClass('wd-opened');
				}

				setContentPosition();
				woodmartThemeModule.$document.trigger('wood-images-loaded');
				return false;
			});

			woodmartThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if (_this.hasClass('wd-opened') && (!$(target).is('.wd-image-hotspot') || !$(target).is('.wd-spot')) && (!$(target).parents().is('.wd-image-hotspot') && !$(target).parents().is('.wd-spot'))) {
					_this.removeClass('wd-opened');
					return false;
				}
			});
		});

		//Image loaded
		$('.wd-spots').each(function() {
			$(this).addClass('wd-loaded');
		});

		setContentPosition();

		woodmartThemeModule.$window
			.off('resize.wdImageHotspot')
			.on('resize.wdImageHotspot', woodmartThemeModule.debounce(setContentPosition, 150));

		function setContentPosition() {
			$('.wd-image-hotspot .hotspot-content, .wd-spot .wd-spot-dropdown').each(function() {
				var content = $(this);
				var isBlock = content.parents('.wp-block-wd-hotspot').length;

				content.removeClass('hotspot-overflow-right hotspot-overflow-left');
				content.attr('style', '');

				var rect = content[0].getBoundingClientRect();
				var offsetLeft = rect.left;
				var offsetRight = woodmartThemeModule.$window.width() - rect.right;

				if (woodmartThemeModule.windowWidth > 768 && !isBlock) {
					if (offsetLeft <= 0) {
						content.addClass('hotspot-overflow-right');
					}
					if (offsetRight <= 0) {
						content.addClass('hotspot-overflow-left');
					}
				}

				if ((woodmartThemeModule.windowWidth <= 768 || isBlock) && (offsetLeft <= 0 || offsetRight <= 0)) {
					var pushRight = offsetLeft <= 0 ? Math.abs(offsetLeft) + 15 : offsetRight - 15;

					content.css('marginLeft', '10px');
					var honorsMarginLeft = Math.abs(content[0].getBoundingClientRect().left - rect.left) > 0.5;
					content.css('marginLeft', '');

					if (honorsMarginLeft) {
						content.css('marginLeft', pushRight + 'px');
					} else {
						content.css('marginRight', -pushRight + 'px');
					}
				}
			});
		}
	};

	$(document).ready(function() {
		woodmartThemeModule.imageHotspot();
	});
})(jQuery);
