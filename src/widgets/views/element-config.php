<?php

use nullref\rbac\assets\FancyTreeAsset;

FancyTreeAsset::register($this);

$this->registerJS(<<<JS
let body = jQuery('body');
body.on('submit', '#elementConfigForm', function (e) {
    e.preventDefault();
    let form = jQuery(this);
    let action = form.prop('action');
    let data = form.serializeArray();
    jQuery.ajax({
        method: 'POST',
        url: action,
        data: data
    }).success(function (response) {
        let wrapper = body.find('.element-config-modal-outer');
        wrapper.find('.modal').modal().hide();
        body.find('.modal-backdrop').remove();
        wrapper.removeClass('shown');
        wrapper.html('');
    }).error(function (response) {
        
    })
});
JS
);

?>

<div class="element-config-modal-outer"></div>
