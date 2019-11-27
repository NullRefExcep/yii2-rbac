<?php

use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $searchModel \nullref\rbac\search\ActionAccessSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $modules array
 */

$this->title = Yii::t('rbac', 'Action Accesses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="action-access-index">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>


    <p>
        <?= Html::a(Yii::t('rbac', 'Create Action Access'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php \yii\widgets\Pjax::begin() ?>

    <div class="table-responsive">
        <?= GridView::widget([
            'filterModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'module',
                    'filter'    => $modules,
                ],
                'controller',
                'action',

                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete}',
                ],
            ],
        ]); ?>
    </div>

    <?php \yii\widgets\Pjax::end() ?>
</div>
