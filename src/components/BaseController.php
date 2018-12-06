<?php

namespace nullref\rbac\components;

use nullref\rbac\filters\AccessControl;
use nullref\core\interfaces\IAdminController;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class BaseController extends Controller implements IAdminController
{
    public function behaviors()
    {
        return ArrayHelper::merge([
            'access' => [
                'class'      => AccessControl::class,
                'controller' => $this,
            ],
        ], parent::behaviors());
    }
}
