jQuery(function () {
    let body = jQuery('body');
    let modalPlacement = body.find('.element-config-modal-outer');

    let modalCallButton = body.find('.modal-control');

    let elements = body.find('[data-identificator]');
    elements.click(function (e) {
        e.preventDefault();
        let current = jQuery(this);
        loadModal(current.data('identificator'));
        return false;
    });
    elements.dblclick(function () {
        let current = jQuery(this);
        current.click();
    });

    let loadModal = function (identificator) {
        jQuery.ajax({
            method: 'GET',
            url: '/rbac/element/element-config',
            data: {
                'identificator': identificator
            }
        }).success(function (response) {
            modalPlacement.html(response);
            modalPlacement.addClass('shown');

            var tree = body.find("#fancyree_itemsTree");
            app.initTree = function () {
                tree.fancytree("getTree").generateFormElements("ElementAccessForm[items][]");
            };
            app.selectTreeNode = app.initTree;
        });

    }
});