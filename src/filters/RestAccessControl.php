<?php

namespace nullref\rbac\filters;

use Yii;
use yii\base\InlineAction;
use yii\filters\AccessRule;
use yii\web\ErrorAction;
use yii\web\Response;

class RestAccessControl extends AccessControl
{
    protected function setDenyCallBack()
    {
        /**
         * @param $rule AccessRule|null
         * @param $action ErrorAction|InlineAction
         *
         * @return Response
         */
        $this->denyCallback = function ($rule, $action) {
            $controller = $action->controller;

            $message = Yii::t('rbac', 'You don\'t have permission to')
                . ' ' . Yii::t('rbac', 'do this action');

            Yii::$app->response->setStatusCode(403);

            return $controller->asJson([
                'message' => $message,
            ]);
        };
    }
}
