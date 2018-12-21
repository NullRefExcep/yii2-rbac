<?php

use nullref\rbac\ar\ElementAccess;
use nullref\rbac\forms\ElementAccessForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model ElementAccessForm
 * @var $tree array
 * @var $elementAccess ElementAccess
 */

$this->title = Yii::t('rbac', 'Update {modelClass}: ', [
        'modelClass' => 'Element Access',
    ]) . ' ' . $elementAccess->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Element Access`s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $elementAccess->id, 'url' => ['view', 'id' => $elementAccess->id]];
$this->params['breadcrumbs'][] = Yii::t('rbac', 'Update');
?>

<div class="element-access-update">

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
                <?= $model->identifier ?>
            </h4>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'List'), ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
        'tree'  => $tree,
        'isNew' => false,
    ]) ?>

</div>
