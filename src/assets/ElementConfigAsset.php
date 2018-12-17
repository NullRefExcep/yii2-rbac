<?php

namespace nullref\rbac\assets;

use yii\web\AssetBundle;

class ElementConfigAsset extends AssetBundle
{
    public $sourcePath = '@nullref/rbac/assets/element-config';
    public $js         = [
        'events.js',
        'modal.js',
    ];
    public $css        = [
        'styles.css'
    ];
    public $depends    = [];
}