<?php

namespace nullref\rbac\controllers;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\ar\FieldAccess;
use nullref\rbac\components\BaseController;
use nullref\rbac\filters\AccessControl;
use nullref\rbac\forms\FieldAccessForm;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use nullref\rbac\services\AuthTreeService;
use nullref\rbac\services\FieldAccessService;
use nullref\rbac\services\FieldReaderService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

/**
 * FieldController implements the CRUD actions for FieldAccess model.
 */
class FieldController extends BaseController
{
    /** @var FieldReaderService */
    private $fieldReader;

    /** @var FieldAccessForm */
    private $fieldAccessForm;

    /** @var FieldAccessService */
    private $fieldAccessService;

    /** @var AuthItemRepository */
    private $authItemRepository;

    /** @var FieldAccessRepositoryInterface */
    private $fieldAccessRepository;

    /** @var AuthTreeService */
    private $authTree;

    public function __construct(
        $id,
        $module,
        $config = [],
        FieldReaderService $fieldReader,
        FieldAccessForm $fieldAccessForm,
        FieldAccessService $fieldAccessService,
        AuthItemRepository $authItemRepository,
        FieldAccessRepositoryInterface $fieldAccessRepository,
        AuthTreeService $authTree
    )
    {
        $this->fieldReader = $fieldReader;
        $this->fieldAccessForm = $fieldAccessForm;
        $this->fieldAccessService = $fieldAccessService;
        $this->authItemRepository = $authItemRepository;
        $this->fieldAccessRepository = $fieldAccessRepository;
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

    public function actionScenarios($selected = '')
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $model = $parents[0];
                $out = $this->fieldReader->getScenariosJs($model);
                echo Json::encode(['output' => $out, 'selected' => $selected]);

                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    public function actionAttributes($selected = '')
    {
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $model = $parents[0];
                $scenario = $parents[1];
                $out = $this->fieldReader->getAttributesJs($model, $scenario);
                echo Json::encode(['output' => $out, 'selected' => $selected]);

                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Lists all FieldAccess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => FieldAccess::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FieldAccess model.
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
     * Creates a new FieldAccess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = $this->fieldAccessForm;
        $models = $this->fieldReader->getModels();
        $tree = $this->authTree->getArrayAuthTreeStructure($this->authTree->getAuthTree());

        if ($model->load(Yii::$app->request->post()) && $saveId = $model->save()) {
            return $this->redirect(['view', 'id' => $saveId]);
        } else {
            return $this->render('create', [
                'model'  => $model,
                'models' => $models,
                'tree'   => $tree,
            ]);
        }
    }

    /**
     * Updates an existing FieldAccess model.
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

        $model = $this->fieldAccessForm;
        $model->loadWithAR($ar);
        $models = $this->fieldReader->getModels();

        $tree = $this->authTree->getArrayAuthTreeStructure(
            $this->authTree->getAuthTree(),
            $this->fieldAccessService->getItems($ar)
        );

        if ($model->load(Yii::$app->request->post()) && $updateId = $model->update($ar)) {
            return $this->redirect(['view', 'id' => $updateId]);
        } else {
            return $this->render('update', [
                'model'       => $model,
                'models'      => $models,
                'tree'        => $tree,
                'fieldAccess' => $ar,
            ]);
        }
    }

    /**
     * Deletes an existing FieldAccess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->fieldAccessRepository->delete(['id' => $id]);

        return $this->redirect(['index']);
    }

    /**
     * Finds the FieldAccess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return ActionAccess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = $this->fieldAccessRepository->findOneWithAuthItems($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
