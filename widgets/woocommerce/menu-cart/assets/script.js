jQuery( window ).on( 'elementor/frontend/init', function() {

	var MiniCartHandler = elementorModules.frontend.handlers.Base.extend({

		getDefaultSettings: function getDefaultSettings() {
			return {
				selectors: {
					container: '.twbb_menu-cart__container',
					toggle: '.twbb_menu-cart__toggle .elementor-button',
					closeButton: '.twbb_menu-cart__close-button',
					cartLink: '#twbb_menu-cart__toggle_button'
				},
				classes: {
					isShown: 'twbb_menu-cart--shown',
					lightbox: 'elementor-lightbox'
				}
			};
		},

		getDefaultElements: function getDefaultElements() {
			var selectors = this.getSettings('selectors'),
				elements = {};

			elements.$container = this.$element.find(selectors.container);
			elements.$toggle = this.$element.find(selectors.toggle);
			elements.$closeButton = this.$element.find(selectors.closeButton);
			elements.$cartLink = this.$element.find(selectors.cartLink);

			return elements;
		},

		bindEvents: function bindEvents() {
			var _elements = this.elements,
				$container = _elements.$container,
				$toggle = _elements.$toggle,
				$closeButton = _elements.$closeButton,
				$cartLink = _elements.$cartLink,
				classes = this.getSettings('classes');

			// Activate full-screen mode on click

			$toggle.on('click', function (event) {
				var currentUrl = window.location.origin + window.location.pathname,
					isCart = $container.data('cart') === currentUrl,
					isCheckout = $container.data('checkout') === currentUrl;

				if (!isCart && !isCheckout) {
					event.preventDefault();
					$container.addClass(classes.isShown);
				} else {
					var cartUrl = $container.data('cart');
					$cartLink.attr('href', cartUrl);
					$container.removeClass(classes.isShown);
				}
			});

			// Deactivate full-screen mode on click or on esc.
			$container.on('click', function (event) {
				if ($container.hasClass(classes.isShown) && $container[0] === event.target) {
					$container.removeClass(classes.isShown);
				}
			});

			$closeButton.on('click', function () {
				$container.removeClass(classes.isShown);
			});

			elementorFrontend.elements.$document.keyup(function (event) {
				var ESC_KEY = 27;

				if (ESC_KEY === event.keyCode) {
					if ($container.hasClass(classes.isShown)) {
						$container.click();
					}
				}
			});
		},

		onInit: function onInit() {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

			this.bindEvents();
		}
	});

	elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-menu-cart.default', function ( $scope ) {
		new MiniCartHandler( { $element: $scope } );
	});

	jQuery(document.body).on('wc_fragments_loaded wc_fragments_refreshed', function () {
		jQuery('div.elementor-widget-twbb_woocommerce-menu-cart').each(function () {
			elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
		});
	});

});