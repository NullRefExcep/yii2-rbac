<?php

use kartik\select2\Select2;
use yii\data\ArrayDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var $this         View
 * @var $searchModel  AuthRuleSearch
 * @var $dataProvider ArrayDataProvider
 */

$this->title = Yii::t('rbac', 'Rules');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="auth-item-index">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'Create rule'), ['/rbac/rule/create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin() ?>

    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => "{items}\n{pager}",
            'columns'      => [
                [
                    'attribute' => 'name',
                    'label'     => Yii::t('rbac', 'Name'),
                    'options'   => [
                        'style' => 'width: 20%',
                    ],
                    'filter'    => Select2::widget([
                        'model'         => $searchModel,
                        'attribute'     => 'name',
                        'options'       => [
                            'placeholder' => Yii::t('rbac', 'Select rule'),
                        ],
                        'pluginOptions' => [
                            'ajax'       => [
                                'url'      => Url::to(['search']),
                                'dataType' => 'json',
                                'data'     => new JsExpression('function(params) { return {q:params.term}; }'),
                            ],
                            'allowClear' => true,

                        ],
                    ]),
                ],
                [
                    'attribute' => 'class',
                    'label'     => Yii::t('rbac', 'Class'),
                    'value'     => function ($row) {
                        $rule = unserialize($row['data']);

                        return get_class($rule);
                    },
                    'options'   => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute' => 'created_at',
                    'label'     => Yii::t('rbac', 'Created at'),
                    'format'    => 'datetime',
                    'options'   => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute' => 'updated_at',
                    'label'     => Yii::t('rbac', 'Updated at'),
                    'format'    => 'datetime',
                    'options'   => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'class'      => ActionColumn::class,
                    'template'   => '{update} {delete}',
                    'urlCreator' => function ($action, $model) {
                        return Url::to(['/rbac/rule/' . $action, 'name' => $model['name']]);
                    },
                    'options'    => [
                        'style' => 'width: 5%',
                    ],
                ],
            ],
        ]) ?>
    </div>
    <?php Pjax::end() ?>
</div>
