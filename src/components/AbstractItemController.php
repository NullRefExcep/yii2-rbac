<?php

namespace nullref\rbac\components;

use nullref\rbac\forms\PermissionForm;
use nullref\rbac\forms\RoleForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

abstract class AbstractItemController extends BaseController
{
    /**
     * @param  string $name
     *
     * @return RoleForm|PermissionForm
     */
    abstract protected function getItem($name);

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass === null) {
            throw new InvalidConfigException('Model class should be set');
        }
        if ($this->type === null) {
            throw new InvalidConfigException('Auth item type should be set');
        }
    }

    /**
     * Lists all created items.
     * @return string
     */
    public function actionIndex()
    {
        $filterModel = new Search($this->type);

        return $this->render('index', [
            'filterModel'  => $filterModel,
            'dataProvider' => $filterModel->search(Yii::$app->request->get()),
        ]);
    }

    /**
     * Shows create form.
     * @return string|Response
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var RoleForm|PermissionForm $model */
        $model = Yii::createObject([
            'class'    => $this->modelClass,
            'scenario' => 'create',
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Shows update form.
     *
     * @param  string $name
     *
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionUpdate($name)
    {
        /** @var RoleForm|PermissionForm $model */
        $item = $this->getItem($name);
        $model = Yii::createObject([
            'class'    => $this->modelClass,
            'scenario' => 'update',
            'item'     => $item,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes item.
     *
     * @param  string $name
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($name)
    {
        $item = $this->getItem($name);
        Yii::$app->authManager->remove($item);

        return $this->redirect(['index']);
    }
}
