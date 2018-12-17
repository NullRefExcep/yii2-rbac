jQuery(function () {
    var elementConfigModal = jQuery('#product-details-modal');

    function getVariantData(id) {
        $.ajax({
            method: 'GET',
            url: '/catalog/default/quick',
            data: {
                'id': id
            }
        }).done(function (html) {
            var modalContent = jQuery(html).filter('.modal-dialog-product');
            quickViewModal.find('.modal-dialog-product').html(modalContent.html());
            quickViewModal.modal('show');
            quickViewModal.find("select").minimalect({
                onchange: function (value, text) {
                    var select = $(event.target).parent().parent().find("select[name='size']");
                    select.val(value).trigger("change");
                    var price = modalContent
                        .find('#variants-prices-hidden')
                        .find('li[data-id="{0}"]'.format(select.find('option:selected').text()))
                        .text();
                    quickViewModal.find('.price-sales .price-value').text(price);
                }
            });
        });
    }


    var body = jQuery('body');
    body.on('click', '.show-modal', function () {
        var id = $(this).data('id');
        getVariantData(id);
    });
});
