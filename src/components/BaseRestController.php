<?php

namespace nullref\rbac\components;

use nullref\rbac\filters\RestAccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class BaseRestController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge([
            'access' => [
                'class'      => RestAccessControl::class,
                'controller' => $this,
            ],
        ], parent::behaviors());
    }
}
