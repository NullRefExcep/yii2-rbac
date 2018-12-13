<?php

namespace nullref\rbac\controllers;

use nullref\rbac\components\BaseController;
use nullref\rbac\components\DBManager;
use nullref\rbac\forms\AssignmentForm;
use nullref\rbac\helpers\UserColumns;
use nullref\rbac\helpers\UserFilter;
use nullref\rbac\repositories\AuthAssignmentRepository;
use nullref\rbac\services\AssignmentService;
use nullref\rbac\services\AuthTreeService;
use Yii;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;

class AssignmentController extends BaseController
{
    /** @var AssignmentService */
    private $assignmentService;

    /** @var DBManager */
    private $manager;

    /** @var AuthAssignmentRepository */
    private $authAssignmentRepository;

    /** @var AuthTreeService */
    private $authTree;

    /** @var array */
    private $users;

    public function __construct(
        $id,
        $module,
        $config = [],
        AssignmentService $assignmentService,
        DBManager $manager,
        AuthAssignmentRepository $authAssignmentRepository,
        AuthTreeService $authTree
    )
    {
        $this->assignmentService = $assignmentService;
        $this->manager = $manager;
        $this->authAssignmentRepository = $authAssignmentRepository;
        $this->authTree = $authTree;

        $this->users = $module->userProvider->getUsers();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $dataProvider = new ArrayDataProvider([
            'allModels'  => $this->users,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        $columns = Yii::createObject(UserColumns::class, [$this->users])->getColumns();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'columns'      => $columns,
        ]);
    }

    public function actionAssign($id)
    {
        $model = Yii::createObject(AssignmentForm::class, [
            $this->manager,
            $this->authAssignmentRepository
        ]);
        $model->userId = $id;

        if ($model->load(Yii::$app->request->post()) && $model->updateAssignments()) {
            return $this->redirect(Url::to(['/rbac/assignment/index']));
        }

        $treeStructure = $this->authTree->getArrayAuthTreeStructure(
            $this->authTree->getAuthTree(),
            $this->assignmentService->getUserAssignments($id)
        );
        $username = Yii::createObject(UserFilter::class, [$this->users])->getUsername($id);

        return $this->render('assign', [
            'model'    => $model,
            'tree'     => $treeStructure,
            'username' => $username,
        ]);
    }
}