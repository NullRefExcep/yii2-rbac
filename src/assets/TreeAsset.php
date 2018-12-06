<?php

namespace nullref\rbac\assets;

use yii\web\AssetBundle;

class TreeAsset extends AssetBundle
{
    public $sourcePath = '@nullref/rbac/assets/tree';
    public $js         = [
        'jquery.mjs.nestedSortable.js',
        'tree.js',
    ];
    public $css        = [
        'tree.css',
    ];
    public $depends    = [
        'yii\jui\JuiAsset',
    ];
}