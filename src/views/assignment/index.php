<?php

use nullref\rbac\search\AssignmentSearch;
use rmrevin\yii\fontawesome\FA;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var $this View
 * @var $dataProvider ArrayDataProvider
 * @var $searchModel AssignmentSearch
 * @var $columns array
 */

$this->title = Yii::t('rbac', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assignments-index">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <?php Pjax::begin() ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => array_merge(
                [['class' => 'yii\grid\SerialColumn']],
                $columns,
                [
                    [
                        'class'    => 'yii\grid\ActionColumn',
                        'template' => '{assign}',
                        'buttons'  => [
                            'assign' => function ($url, $model) {
                                return Html::a(FA::icon(FA::_GAVEL), [
                                    'assign',
                                    'id' => $model['id'],
                                ], [
                                    'title'      => Yii::t('rbac', 'Assign'),
                                    'aria-label' => Yii::t('rbac', 'Assign'),
                                    'data-pjax' => '0',
                                ]);
                            },
                        ],
                    ],
                ]
            ),
        ]); ?>
    </div>

    <?php Pjax::end() ?>
</div>
