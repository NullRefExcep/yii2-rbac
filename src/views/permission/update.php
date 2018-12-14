<?php

use nullref\rbac\forms\PermissionForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $model PermissionForm
 * @var $rules array
 * @var $this  View
 */

$this->title = Yii::t('rbac', 'Update permission');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="permission-update">

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
                'rules' => $rules,
            ]) ?>
        </div>
    </div>
</div>
