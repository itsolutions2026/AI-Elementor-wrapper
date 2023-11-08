jQuery( window ).on( 'elementor/frontend/init', function() {
  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_woocommerce-page.default', function ( $scope ) {
    var single_product = jQuery('body .elementor-widget-twbb_woocommerce-page');
    if( single_product.length > 1 ) {
      alert("The page already includes a WooCommerce Pages Widget element.");
      elementor.getPanelView().getCurrentPageView().getOption('editedElementView').removeElement();
    }
  });
});
