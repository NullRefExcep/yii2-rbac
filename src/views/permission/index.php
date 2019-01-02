<?php

use kartik\select2\Select2;
use nullref\rbac\search\AuthItemSearch;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var $dataProvider array
 * @var $this         View
 * @var $searchModel AuthItemSearch
 * @var $items array
 * @var $rules array
 */

$this->title = Yii::t('rbac', 'Permissions');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="permission-index">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'Create permission'), ['/rbac/permission/create'], ['class' => 'btn btn-success']) ?>
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
                    'header'    => Yii::t('rbac', 'Name'),
                    'options'   => [
                        'style' => 'width: 20%',
                    ],
                    'filter'    => Select2::widget([
                        'model'         => $searchModel,
                        'attribute'     => 'name',
                        'data'          => $items,
                        'options'       => [
                            'placeholder' => Yii::t('rbac', 'Select permission'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]),
                ],
                [
                    'attribute'          => 'description',
                    'header'             => Yii::t('rbac', 'Description'),
                    'options'            => [
                        'style' => 'width: 55%',
                    ],
                    'filterInputOptions' => [
                        'class'       => 'form-control',
                        'id'          => null,
                        'placeholder' => Yii::t('rbac', 'Enter the description'),
                    ],
                ],
                [
                    'attribute' => 'rule_name',
                    'header'    => Yii::t('rbac', 'Rule Name'),
                    'options'   => [
                        'style' => 'width: 20%',
                    ],
                    'filter'    => Select2::widget([
                        'model'         => $searchModel,
                        'attribute'     => 'rule_name',
                        'data'          => $rules,
                        'options'       => [
                            'placeholder' => Yii::t('rbac', 'Select rule'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]),
                ],
                [
                    'class'      => ActionColumn::class,
                    'template'   => '{update} {delete}',
                    'urlCreator' => function ($action, $model) {
                        return Url::to(['/rbac/permission/' . $action, 'name' => $model['name']]);
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
