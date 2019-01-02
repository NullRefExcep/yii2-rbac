<?php

use nullref\rbac\search\AuthItemSearch;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var $this View
 * @var $dataProvider array
 * @var $searchModel AuthItemSearch
 */

$this->title = Yii::t('rbac', 'Roles');
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
        <?= Html::a(Yii::t('rbac', 'Create role'), ['/rbac/role/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin() ?>
    <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                [
                    'attribute' => 'name',
                    'header'    => Yii::t('rbac', 'Name'),
                    'options'   => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute' => 'description',
                    'header'    => Yii::t('rbac', 'Description'),
                    'options'   => [
                        'style' => 'width: 55%',
                    ],
                ],
                [
                    'attribute' => 'rule_name',
                    'header'    => Yii::t('rbac', 'Rule Name'),
                    'options'   => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'class'      => ActionColumn::class,
                    'template'   => '{update} {delete}',
                    'urlCreator' => function ($action, $model) {
                        return Url::to(['/rbac/role/' . $action, 'name' => $model['name']]);
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