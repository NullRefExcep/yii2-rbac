<?php

namespace nullref\rbac\controllers;

use nullref\rbac\ar\ElementAccess;
use nullref\rbac\components\BaseController;
use nullref\rbac\filters\AccessControl;
use nullref\rbac\forms\ElementAccessForm;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\interfaces\ElementAccessRepositoryInterface;
use nullref\rbac\services\AuthTreeService;
use nullref\rbac\services\ElementAccessService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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

    /** @var ElementAccessRepositoryInterface */
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
        ElementAccessRepositoryInterface $elementAccessRepository,
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
     * @param $identifier
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionElementConfig($identifier)
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = $this->elementAccessForm;
        $ar = $this->elementAccessRepository->findOneByCondition(['identifier' => $identifier]);
        $elementItems = [];
        if ($ar) {
            $model->loadWithAR($ar);
            $elementItems = $this->elementAccessService->getItems($ar);
        }

        $items = $this->authItemRepository->getMap('name', 'name');
        $selected = Json::encode($elementItems);

        return $this->renderAjax('element-config', [
            'model'         => $model,
            'items'          => $items,
            'selected'      => $selected,
            'elementAccess' => $ar,
        ]);
    }

    /**
     * @param $identifier
     *
     * @return array|bool
     * @throws NotFoundHttpException
     */
    public function actionSaveAjax($identifier)
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = $this->elementAccessForm;
        $ar = $this->elementAccessRepository->findOneByCondition(['identifier' => $identifier]);

        $result = false;
        if ($ar) {
            $model->loadWithAR($ar);
            if ($model->load(Yii::$app->request->post()) && $updateId = $model->update($ar)) {
                $result = [
                    'status' => 'success',
                ];
            }
        } else {
            if ($model->load(Yii::$app->request->post()) && $saveId = $model->save()) {
                $result = [
                    'status' => 'success',
                ];
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($result) {
            return $result;
        } else {
            return [
                'status' => 'error',
            ];
        }
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
        $tree = $this->authTree->getArrayAuthTreeStructure($this->authTree->getAuthTree());

        if ($model->load(Yii::$app->request->post()) && $saveId = $model->save()) {
            return $this->redirect(['view', 'id' => $saveId]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'tree'  => $tree,
            ]);
        }
    }

    /**
     * Updates an existing ElementAccess model.
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
            $this->elementAccessService->getItems($ar)
        );

        if ($model->load(Yii::$app->request->post()) && $updateId = $model->update($ar)) {
            return $this->redirect(['view', 'id' => $updateId]);
        } else {
            return $this->render('update', [
                'model'         => $model,
                'tree'          => $tree,
                'elementAccess' => $ar,
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
