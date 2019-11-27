<?php

namespace nullref\rbac\controllers;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\components\BaseController;
use nullref\rbac\filters\AccessControl;
use nullref\rbac\forms\ActionAccessAssignForm;
use nullref\rbac\forms\ActionAccessForm;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\interfaces\ActionAccessRepositoryInterface;
use nullref\rbac\search\ActionAccessSearch;
use nullref\rbac\services\ActionAccessService;
use nullref\rbac\services\ActionReaderService;
use nullref\rbac\services\AuthTreeService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

/**
 * AccessController implements the CRUD actions for ControllerAccess model.
 */
class AccessController extends BaseController
{
    /** @var ActionReaderService */
    private $actionReader;

    /** @var ActionAccessForm */
    private $acFrom;

    /** @var ActionAccessAssignForm */
    private $acaFrom;

    /** @var ActionAccessService */
    private $actionAccessService;

    /** @var AuthItemRepository */
    private $authItemRepository;

    /** @var ActionAccessRepositoryInterface */
    private $actionAccessRepository;

    /** @var AuthTreeService */
    private $authTree;

    public function __construct(
        $id,
        $module,
        $config = [],
        ActionReaderService $actionReader,
        ActionAccessForm $acFrom,
        ActionAccessAssignForm $acaFrom,
        ActionAccessService $actionAccessService,
        AuthItemRepository $authItemRepository,
        ActionAccessRepositoryInterface $actionAccessRepository,
        AuthTreeService $authTree
    )
    {
        $this->actionReader = $actionReader;
        $this->acFrom = $acFrom;
        $this->acaFrom = $acaFrom;
        $this->actionAccessService = $actionAccessService;
        $this->authItemRepository = $authItemRepository;
        $this->actionAccessRepository = $actionAccessRepository;
        $this->authTree = $authTree;

        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class'      => AccessControl::class,
                'controller' => $this,
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionControllers($selected = '')
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $module = $parents[0];
                $out = $this->actionReader->getControllersJs($module);

                return Json::encode(['output' => $out, 'selected' => $selected]);
            }
        }

        return Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionActions($selected = '')
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $module = $parents[0];
                $controller = $parents[1];
                $out = $this->actionReader->getActionsJs($module, $controller);

                return Json::encode(['output' => $out, 'selected' => $selected]);
            }
        }

        return Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Lists all ControllerAccess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ActionAccessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $modules = (new ActionReaderService())->getModules();

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'modules'      => $modules,
        ]);
    }

    /**
     * Displays a single ControllerAccess model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ControllerAccess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->acFrom;
        $modules = $this->actionReader->getModules();
        $treeStructure = $this->authTree->getArrayAuthTreeStructure($this->authTree->getAuthTree());

        if ($model->load(Yii::$app->request->post()) && $saveId = $model->save()) {
            return $this->redirect(['view', 'id' => $saveId]);
        } else {
            return $this->render('create', [
                'model'   => $model,
                'modules' => $modules,
                'tree'    => $treeStructure,
            ]);
        }
    }

    /**
     * Updates an existing ActionAccess model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $ar = $this->findModel($id);

        $model = $this->acFrom;
        $model->loadWithAR($ar);
        $modules = $this->actionReader->getModules();

        $tree = $this->authTree->getArrayAuthTreeStructure(
            $this->authTree->getAuthTree(),
            $this->actionAccessService->getItems($ar)
        );

        if ($model->load(Yii::$app->request->post()) && $updateId = $model->update($ar)) {
            return $this->redirect(['view', 'id' => $updateId]);
        } else {
            return $this->render('update', [
                'model'        => $model,
                'modules'      => $modules,
                'tree'         => $tree,
                'actionAccess' => $ar,
            ]);
        }
    }

    /**
     * Deletes an existing ControllerAccess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->actionAccessRepository->delete(['id' => $id]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the ControllerAccess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return ActionAccess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = $this->actionAccessRepository->findOneWithAuthItems($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
