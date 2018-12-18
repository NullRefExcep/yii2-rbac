jQuery(function () {
    let body = jQuery('body');
    let modalPlacement = body.find('.element-config-modal-outer');
    let modalControl = body.find('.modal-control');

    let elements = body.find('[data-identificator]');
    elements.click(function (e) {
        if (e.ctrlKey) {
            e.preventDefault();
            let current = jQuery(this);
            loadModal(current);
            return false;
        }
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
            body.find('.modal-header h3').text(identificator);
            let form = body.find('#elementConfigForm');
            form.prop('action', form.prop('action') + '?identificator=' + identificator);
            body.find('#elementIdentificator').val(identificator);
            modalPlacement.find('.modal').modal().show();

            //Select items
            if (app.selection != {}) {
                let select = body.find('#elementItems');
                select.select2('val', [app.selection]);
            }
        });
    }
});