jQuery(function () {
    let body = jQuery('body');
    let modalPlacement = body.find('.element-config-modal-outer');

    let elements = body.find('[data-identificator]');
    elements.click(function (e) {
        e.preventDefault();
        let current = jQuery(this);
        loadModal(current);
        return false;
    });
    elements.dblclick(function () {
        let current = jQuery(this);
        current.click();
    });

    let loadModal = function (caller) {
        let identificator = caller.data('identificator');
        jQuery.ajax({
            method: 'GET',
            url: '/rbac/element/element-config',
            data: {
                'identificator': identificator
            }
        }).success(function(response) {
            modalPlacement.html(response);
            modalPlacement.addClass('shown');
            body.find('.modal-header h3').text(identificator);
            let form = body.find('#elementConfigForm');
            form.prop('action', form.prop('action') + '?identificator=' + identificator);
            body.find('#elementIdentificator').val(identificator);
        });
    }
});