<?php

namespace nullref\rbac\controllers;

use nullref\rbac\components\AbstractItemController;
use nullref\rbac\forms\PermissionForm;
use nullref\rbac\models\AuthItemChild;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\RuleRepository;
use nullref\rbac\search\AuthItemSearch;
use Yii;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\NotFoundHttpException;

class PermissionController extends AbstractItemController
{
    /** @var string */
    protected $modelClass = PermissionForm::class;

    /** @var */
    protected $type = Item::TYPE_PERMISSION;

    /** @var AuthItemRepository */
    private $aiRepository;

    /** @var RuleRepository */
    private $rRepository;

    public function __construct(
        $id,
        $module,
        $config = [],
        AuthItemRepository $authItemRepository,
        RuleRepository $ruleRepository
    )
    {
        $this->aiRepository = $authItemRepository;
        $this->rRepository = $ruleRepository;

        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $searchModel = new AuthItemSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'items'        => $this->aiRepository->getMapByType($this->type, 'name', 'description'),
            'rules'        => $this->rRepository->getMap('name', 'name'),
        ]);
    }
    
    /** @inheritdoc */
    protected function getItem($name)
    {
        $permission = Yii::$app->authManager->getPermission($name);

        if ($permission instanceof Permission) {
            return $permission;
        }

        throw new NotFoundHttpException;
    }
}