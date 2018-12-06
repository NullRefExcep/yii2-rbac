jQuery(function () {
    jQuery(document).on('click', '.collapse-list-btn', function () {
        jQuery(this).find('i').toggleClass('fa-arrow-down').toggleClass('fa-arrow-up');
    });

    var treeView = jQuery('#treeView');
    var formName = treeView.data('formName');

    treeView.find('> ol').nestedSortable({
        items: '.auth-list-item',
        helper: 'clone',
        handle: '.drag-btn',
        toleranceElement: '> div',
        isTree: true,
        update: function (e, obj) {
            treeView.css({opacity: 0.4});
            var url = obj.item.data('updateUrl');
            var parent = obj.item.parents('li');
            var data = {};
            data[formName] = {
                parentName: parent.length ? parent.data('name') : ''
            };
            jQuery.ajax({
                url: url,
                method: 'post',
                data: data,
                success: function () {
                    treeView.css({opacity: 1});
                },
                error: function () {
                }
            });
            updateCollapseButtons();
        }
    }).disableSelection();

    function updateCollapseButtons() {
        console.log('trigger');
        var items = treeView.find('li');
        items.each(function () {
            var item = jQuery(this);
            var btn = item.find('> div .collapse-list-btn');
            var list = item.find('> ol');
            if (list.children().length) {
                btn.removeClass('disabled');
                if (list.hasClass('in')) {
                    btn.find('i').removeClass('fa-arrow-down').addClass('fa-arrow-up');
                } else {
                    btn.find('i').removeClass('fa-arrow-up').addClass('fa-arrow-down');
                }
            } else {
                btn.addClass('btn-default');
            }
        });
    }
});