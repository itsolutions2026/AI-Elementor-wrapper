jQuery( window ).on( 'elementor/frontend/init', function () {
    jQuery('.twbb_woocommerce-products-ajax-paginate .page-numbers li').on('click', function(e) {
        e.preventDefault();
        ProductsAjaxPagination(jQuery(this));
    })
})

function ProductsAjaxPagination(element) {
    const url = element.find('a').attr('href');
    const container = element.closest('.elementor-widget-twbb_woocommerce-products');
    const container_id = element.closest('.elementor-widget-twbb_woocommerce-products').data('id');
    jQuery.ajax({
        url: url,
        type:'GET',
        dataType: 'html',
        success: function(data){
            let parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');
            const new_page = jQuery(doc).find('.elementor-widget-twbb_woocommerce-products[data-id="' + container_id + '"]').html();
            container.html(new_page);
            jQuery('.twbb_woocommerce-products-ajax-paginate .page-numbers li').on('click', function(e) {
                e.preventDefault();
                ProductsAjaxPagination(jQuery(this));
            })
        }
    })
}
