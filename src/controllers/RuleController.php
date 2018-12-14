<?php

namespace nullref\rbac\controllers;

use nullref\rbac\components\BaseController;
use nullref\rbac\components\DBManager;
use nullref\rbac\components\RuleManager;
use nullref\rbac\forms\RuleForm;
use nullref\rbac\repositories\RuleRepository;
use nullref\rbac\search\AuthRuleSearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\rbac\Rule;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class RuleController extends BaseController
{
    /** @var DBManager */
    private $manager;

    /** @var RuleRepository */
    private $ruleRepository;

    /** @var RuleManager */
    private $ruleManager;

    /**
     * RuleController constructor.
     *
     * @param $id
     * @param $module
     * @param array $config
     * @param DBManager $manager
     * @param RuleRepository $ruleRepository
     */
    public function __construct(
        $id,
        $module,
        $config = [],
        DBManager $manager,
        RuleRepository $ruleRepository
    )
    {
        $this->manager = $manager;
        $this->ruleRepository = $ruleRepository;

        $this->ruleManager = $module->ruleManager;

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
        $model = Yii::createObject([
            'class'    => RuleForm::class,
            'scenario' => RuleForm::SCENARIO_CREATE,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->create()) {
            Yii::$app->session->setFlash('success', Yii::t('rbac', 'Rule has been added'));

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model'       => $model,
            'ruleManager' => $this->ruleManager,
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
        $model = Yii::createObject([
            'class'    => RuleForm::class,
            'scenario' => RuleForm::SCENARIO_UPDATE,
        ]);
        $rule = $this->findRule($name);

        $model->setOldName($name);
        $model->setAttributes([
            'name'  => $rule->name,
            'class' => get_class($rule),
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('success', Yii::t('rbac', 'Rule has been updated'));

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model'       => $model,
            'ruleManager' => $this->ruleManager,
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
            'results' => $this->ruleRepository->getRuleNames($q),
        ];
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
