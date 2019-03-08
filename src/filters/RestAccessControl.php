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
        //TODO make json response
        /**
         * @param $rule AccessRule|null
         * @param $action ErrorAction|InlineAction
         *
         * @return Response
         */
        $this->denyCallback = function ($rule, $action) {
            $controller = $action->controller;
            if ($this->userComponent->isGuest) {
                return $controller->redirect($this->loginUrl);
            }
            Yii::$app->session->setFlash('warning', Yii::t('rbac', 'You don\'t have permission to')
                . ' ' . Yii::t('rbac', 'do this action'));

            return $controller->redirect(Yii::$app->request->referrer ?? Yii::$app->getHomeUrl());
        };
    }
}
