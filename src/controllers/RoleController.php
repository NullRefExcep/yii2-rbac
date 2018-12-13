<?php

namespace nullref\rbac\controllers;

use nullref\rbac\components\AbstractItemController;
use nullref\rbac\forms\RoleForm;
use nullref\rbac\models\AuthItemChild;
use nullref\rbac\search\AuthItemSearch;
use Yii;
use yii\rbac\Item;
use yii\rbac\Role;
use yii\web\NotFoundHttpException;

class RoleController extends AbstractItemController
{
    /** @var string */
    protected $modelClass = RoleForm::class;

    /** @var int */
    protected $type = Item::TYPE_ROLE;
    
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /** @inheritdoc */
    protected function getItem($name)
    {
        $role = Yii::$app->authManager->getRole($name);

        if ($role instanceof Role) {
            return $role;
        }

        throw new NotFoundHttpException;
    }
}