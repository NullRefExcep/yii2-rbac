<?php

use nullref\rbac\ar\FieldAccess;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var $this View
 * @var $model FieldAccess
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Field Access`s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-access-view">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'List'), ['index'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('rbac', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('rbac', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data'  => [
                'confirm' => Yii::t('rbac', 'Are you sure you want to delete this item?'),
                'method'  => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model'      => $model,
        'attributes' => [
            'id',
            'model_name',
            'scenario_name',
            'attribute_name',
            'description',
        ],
    ]) ?>

</div>
