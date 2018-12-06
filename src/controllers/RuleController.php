<?php

namespace nullref\rbac\controllers;

use nullref\rbac\components\BaseController;
use nullref\rbac\components\DbManager;
use nullref\rbac\forms\RuleForm;
use nullref\rbac\repositories\RuleRepository;
use nullref\rbac\search\AuthRuleSearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class RuleController extends BaseController
{
    /** @var DbManager */
    private $manager;

    /** @var RuleRepository */
    private $rRepository;

    /**
     * RuleController constructor.
     *
     * @param $id
     * @param $module
     * @param array $config
     * @param DbManager $manager
     */
    public function __construct(
        $id,
        $module,
        $config = [],
        DbManager $manager,
        RuleRepository $ruleRepository
    )
    {
        $this->manager = $manager;
        $this->rRepository = $ruleRepository;

        parent::__construct($id, $module, $config);
    }

    /**
     * Shows list of created rules.
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $searchModel = $this->getSearchModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Shows page where new rule can be added.
     *
     * @return array|string
     */
    public function actionCreate()
    {
        $model = $this->getModel(RuleForm::SCENARIO_CREATE);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->create()) {
            Yii::$app->session->setFlash('success', Yii::t('rbac', 'Rule has been added'));

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates existing auth rule.
     *
     * @param  string $name
     *
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($name)
    {
        $model = $this->getModel(RuleForm::SCENARIO_UPDATE);
        $rule = $this->findRule($name);

        $model->setOldName($name);
        $model->setAttributes([
            'name'  => $rule->name,
            'class' => get_class($rule),
        ]);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('success', Yii::t('rbac', 'Rule has been updated'));

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Removes rule.
     *
     * @param  string $name
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDelete($name)
    {
        $rule = $this->findRule($name);

        $this->manager->remove($rule);
        $this->manager->invalidateCache();

        Yii::$app->session->setFlash('success', Yii::t('rbac', 'Rule has been removed'));

        return $this->redirect(['index']);
    }

    /**
     * Searches for rules.
     *
     * @param  string|null $q
     *
     * @return array
     */
    public function actionSearch($q = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'results' => $this->rRepository->getRuleNames($q),
        ];
    }

    /**
     * @param  string $scenario
     *
     * @return Rule
     * @throws InvalidConfigException
     */
    private function getModel($scenario)
    {
        return Yii::createObject([
            'class'    => RuleForm::class,
            'scenario' => $scenario,
        ]);
    }

    /**
     * @return AuthRuleSearch
     * @throws InvalidConfigException
     */
    private function getSearchModel()
    {
        return Yii::createObject(AuthRuleSearch::class);
    }

    /**
     * @param  string $name
     *
     * @return mixed|null|
     * @throws NotFoundHttpException
     */
    private function findRule($name)
    {
        $rule = $this->manager->getRule($name);

        if ($rule instanceof Rule) {
            return $rule;
        }

        throw new NotFoundHttpException(Yii::t('rbac', 'Not found'));
    }
}