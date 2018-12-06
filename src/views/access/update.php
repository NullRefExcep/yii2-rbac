<?php

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\forms\ActionAccessForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model ActionAccessForm
 * @var $modules array
 * @var $tree array
 * @var $actionAccess ActionAccess
 */

$this->title = Yii::t('rbac', 'Update {modelClass}: ', [
        'modelClass' => 'Action Access',
    ]) . ' ' . $actionAccess->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Action Accesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $actionAccess->id, 'url' => ['view', 'id' => $actionAccess->id]];
$this->params['breadcrumbs'][] = Yii::t('rbac', 'Update');
?>

<div class="action-access-update">

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
                <?= $model->module . ' / ' . $model->controller . ' / ' . $model->action ?>
            </h4>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'List'), ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= $this->render('_form', [
        'model'   => $model,
        'modules' => $modules,
        'tree'    => $tree,
        'isNew'   => false,
    ]) ?>

</div>
