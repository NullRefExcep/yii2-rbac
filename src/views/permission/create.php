<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var $model PermissionForm
 * @var $this  View
 */

$this->title = Yii::t('rbac', 'Create new permission');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="permission-create">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'List'), ['/rbac/permission/index'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
        <div class="col-lg-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
