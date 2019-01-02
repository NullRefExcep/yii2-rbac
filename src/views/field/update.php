<?php

use nullref\rbac\ar\FieldAccess;
use nullref\rbac\forms\FieldAccessForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model FieldAccessForm
 * @var $models array
 * @var $tree array
 * @var $fieldAccess FieldAccess
 */

$this->title = Yii::t('rbac', 'Update {modelClass}: ', [
        'modelClass' => 'Field Access',
    ]) . ' ' . $fieldAccess->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Field Access'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $fieldAccess->id, 'url' => ['view', 'id' => $fieldAccess->id]];
$this->params['breadcrumbs'][] = Yii::t('rbac', 'Update');
?>

<div class="field-access-update">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h4>
                <?= $model->modelName . ' ' .  $model->scenarioName . ' ' . $model->attributeName ?>
            </h4>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'List'), ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= $this->render('_form', [
        'model'  => $model,
        'models' => $models,
        'tree'   => $tree,
        'isNew'  => false,
    ]) ?>

</div>
