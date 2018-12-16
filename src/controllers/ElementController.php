<?php

namespace nullref\rbac\controllers;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\ar\ElementAccess;
use nullref\rbac\components\BaseController;
use nullref\rbac\filters\AccessControl;
use nullref\rbac\forms\ElementAccessForm;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\ElementAccessRepository;
use nullref\rbac\services\AuthTreeService;
use nullref\rbac\services\ElementAccessService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * ElementController implements the CRUD actions for ElementAccess model.
 */
class ElementController extends BaseController
{
    /** @var ElementAccessForm */
    private $elementAccessForm;

    /** @var ElementAccessService */
    private $elementAccessService;

    /** @var AuthItemRepository */
    private $authItemRepository;

    /** @var ElementAccessRepository */
    private $elementAccessRepository;

    /** @var AuthTreeService */
    private $authTree;

    public function __construct(
        $id,
        $module,
        $config = [],
        ElementAccessForm $elementAccessForm,
        ElementAccessService $elementAccessService,
        AuthItemRepository $authItemRepository,
        ElementAccessRepository $elementAccessRepository,
        AuthTreeService $authTree
    )
    {
        $this->elementAccessForm = $elementAccessForm;
        $this->elementAccessService = $elementAccessService;
        $this->authItemRepository = $authItemRepository;
        $this->elementAccessRepository = $elementAccessRepository;
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

    /**
     * Lists all ElementAccess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ElementAccess::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ElementAccess model.
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
     * Creates a new ElementAccess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->elementAccessForm;
        $treeStructure = $this->authTree->getArrayAuthTreeStructure($this->authTree->getAuthTree());
        $types = [];

        if ($model->load(Yii::$app->request->post()) && $saveId = $model->save()) {
            return $this->redirect(['view', 'id' => $saveId]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'tree'  => $treeStructure,
                'types' => $types,
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

        $model = $this->elementAccessForm;
        $model->loadWithAR($ar);

        $tree = $this->authTree->getArrayAuthTreeStructure(
            $this->authTree->getAuthTree(),
            $this->actionAccessService->getItems($ar)
        );
        $types = [];

        if ($model->load(Yii::$app->request->post()) && $updateId = $model->update($ar)) {
            return $this->redirect(['view', 'id' => $updateId]);
        } else {
            return $this->render('update', [
                'model'        => $model,
                'tree'         => $tree,
                'types' => $types,
                'actionAccess' => $ar,
            ]);
        }
    }

    /**
     * Deletes an existing ElementAccess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->elementAccessRepository->delete(['id' => $id]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the ElementAccess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return ActionAccess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = $this->elementAccessRepository->findOneWithAuthItems($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
