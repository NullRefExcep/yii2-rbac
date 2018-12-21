jQuery(function () {
    let body = jQuery('body');
    let modalPlacement = body.find('.element-config-modal-outer');
    let modalControl = body.find('.modal-control');

    let elements = body.find('[data-identifier]');
    elements.click(function (e) {
        if (e.ctrlKey) {
            e.preventDefault();
            let current = jQuery(this);
            loadModal(current);
            return false;
        }
    });

    let loadModal = function (caller) {
        let identifier = caller.data('identifier');
        jQuery.ajax({
            method: 'GET',
            url: '/rbac/element/element-config',
            data: {
                'identifier': identifier
            }
        }).success(function(response) {
            modalPlacement.html(response);
            body.find('.modal-header h3').text(identifier);
            let form = body.find('#elementConfigForm');
            form.prop('action', form.prop('action') + '?identifier=' + identifier);
            body.find('#elementIdentifier').val(identifier);
            modalPlacement.find('.modal').modal().show();

            //Select items
            if (app.selection != {}) {
                let select = body.find('#elementItems');
                select.select2('val', [app.selection]);
            }
        });
    }
});