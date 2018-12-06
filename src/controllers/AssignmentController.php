<?php

namespace nullref\rbac\controllers;

use nullref\rbac\components\BaseController;
use nullref\rbac\forms\AssignmentForm;
use Yii;

class AssignmentController extends BaseController
{
    public function actionAssign($userId)
    {
        $form = Yii::createObject([
            'class'  => AssignmentForm::class,
            'userId' => $userId,
        ]);

        if ($form->load(Yii::$app->request->post()) && $form->updateAssignments()) {

        }

        //TODO
    }
}