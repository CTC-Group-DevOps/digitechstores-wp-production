/* global woodmartThemeModule */
woodmartThemeModule.$document.on('wdSearchFullScreenContentLoaded wdShopPageInit wdRecentlyViewedProductLoaded wdProductsTabsLoaded', function() {
	woodmartThemeModule.mobileCarouselSimple();
});

woodmartThemeModule.mobileCarouselSimple = function() {
	var isRtl = woodmartThemeModule.$body.hasClass('rtl');

	document.querySelectorAll('.wd-carousel-container.wd-carousel-dis-mb:not(.wd-carousel-simple-mb)').forEach(function(container) {
		if (woodmartThemeModule.windowWidth > 1024) {
			return;
		}

		container.classList.add('wd-carousel-simple-mb');

		var carouselWrap = container.querySelector('.wd-carousel-wrap');
		var prevArrow    = container.querySelector('.wd-btn-arrow.wd-prev');
		var nextArrow    = container.querySelector('.wd-btn-arrow.wd-next');

		if (!carouselWrap || !prevArrow || !nextArrow) {
			return;
		}

		function updateArrows() {
			if (woodmartThemeModule.windowWidth > 1024) {
				return;
			}

			var scrollLeft  = carouselWrap.scrollLeft;
			var clientWidth = carouselWrap.clientWidth;
			var scrollWidth = carouselWrap.scrollWidth;

			if (scrollWidth <= clientWidth) {
				prevArrow.classList.add('wd-hide');
				nextArrow.classList.add('wd-hide');
				return;
			}

			var normalScroll = isRtl
				? (scrollLeft <= 0 ? Math.abs(scrollLeft) : scrollWidth - clientWidth - scrollLeft)
				: scrollLeft;

			prevArrow.classList.toggle('wd-disabled', normalScroll <= 1);
			nextArrow.classList.toggle('wd-disabled', normalScroll + clientWidth >= scrollWidth - 1);
		}

		function onArrowClick(e, direction) {
			if (woodmartThemeModule.windowWidth > 1024) {
				return;
			}

			e.preventDefault();

			var clientWidth = carouselWrap.clientWidth;
			var scrollWidth = carouselWrap.scrollWidth;
			var maxScroll   = scrollWidth - clientWidth;

			var currentScroll = Math.max(0, Math.min(carouselWrap.scrollLeft, maxScroll));

			var delta = direction === 'next' ? clientWidth : -clientWidth;

			if (isRtl) {
				delta = -delta;
			}

			var target = Math.max(0, Math.min(currentScroll + delta, maxScroll));

			carouselWrap.scrollTo({ left: target, behavior: 'smooth' });
		}

		var arrowsUpdateScheduled = false;

		function scheduleArrowsUpdate() {
			if (arrowsUpdateScheduled) {
				return;
			}

			arrowsUpdateScheduled = true;

			requestAnimationFrame(function() {
				updateArrows();
				arrowsUpdateScheduled = false;
			});
		}

		prevArrow.addEventListener('click', function(e) { onArrowClick(e, 'prev'); });
		nextArrow.addEventListener('click', function(e) { onArrowClick(e, 'next'); });

		carouselWrap.addEventListener('scroll', scheduleArrowsUpdate, { passive: true });

		updateArrows();
	});
};

window.addEventListener('load', function() {
	woodmartThemeModule.mobileCarouselSimple();
});
