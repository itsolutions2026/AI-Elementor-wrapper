jQuery( window ).on( 'elementor/frontend/init', function() {

	function getInitialSlide ( settings ) {
		return Math.floor( ( settings.slides_count - 1 ) / 2 );
	}

	function getSlidesToScroll ( settings ) {
		return Math.min( settings.slides_count, +settings.slides_to_scroll || 1 );
	}

	function getDeviceSlidesPerView( view , settings ) {
		var str = "slides_per_view" + ("desktop" === view ? "" : "_" + view);
		var num =	Math.min( settings.slides_count, +settings[str] || settings['slidesPerView'][view] );
		return num;
	}

	function getSpaceBetween( view, settings ) {
		var str = "space_between";
		return view && "desktop" !== view && (str += "_" + view), settings.breakpoints[str].size || 0;
	}

	elementorFrontend.hooks.addAction('frontend/element_ready/twbb-testimonial-carousel.default',  function () {
		jQuery('.tenweb-testimonial-carousel-swiper').each(async function(i,elem) {

			var id = jQuery(elem).parents('.elementor-widget-twbb-testimonial-carousel').attr('data-id');
			jQuery(elem).attr('id', 'tenweb-testimonial-carousel-swiper-' + id);
			var settings = jQuery(elem).data('settings');

			if ( ! jQuery.isEmptyObject(settings) ) {

				settings.slidesPerView = {
					desktop: 1,
					tablet: 1,
					mobile: 1
				};
				var swiperOptions = {
					navigation: {
						prevEl: '.tenweb-swiper-button-prev',
						nextEl: '.tenweb-swiper-button-next'
					},
					pagination: {
						el: '.swiper-pagination',
						type: settings.pagination,
						clickable: true
					},
					grabCursor: true,
					speed: settings.speed,
					effect: 'slide',
					initialSlide: getInitialSlide( settings ),
					slidesPerView: getDeviceSlidesPerView( 'desktop', settings ),
					loop: 'yes' === settings.loop,
					loopedSlides:settings.slides_count,
					slidesPerGroup: getSlidesToScroll( settings ),
					spaceBetween: getSpaceBetween( '', settings ),
					breakpoints: {
						1280: {
							slidesPerView: getDeviceSlidesPerView( 'desktop', settings ),
							spaceBetween: getSpaceBetween( 'desktop', settings )
						},
						768: {
							slidesPerView: getDeviceSlidesPerView( 'tablet', settings ),
							spaceBetween: getSpaceBetween( 'tablet', settings )
						},
						320: {
							slidesPerView: getDeviceSlidesPerView( 'mobile', settings ),
							spaceBetween: getSpaceBetween( 'mobile', settings )
						}
					}
				}

				if ( settings.autoplay == 'yes' ) {
					swiperOptions.autoplay = {
						delay: settings.autoplay_speed,
						disableOnInteraction: !! settings.pause_on_interaction
					}
				}

				const Swiper = elementorFrontend.utils.swiper;
				await new Swiper( jQuery('#tenweb-testimonial-carousel-swiper-' + id), swiperOptions );
			}
		});
	});

});