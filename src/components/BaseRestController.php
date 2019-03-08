<?php

namespace nullref\rbac\components;

use nullref\core\interfaces\IAdminController;
use nullref\rbac\filters\RestAccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class BaseRestController extends Controller implements IAdminController
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
