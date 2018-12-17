<?php

namespace nullref\rbac\widgets;

use yii\base\Widget;

class ElementConfig extends Widget
{
    /** @inheritdoc */
    public function run()
    {
        return $this->render('element-config');
    }
}