<?php

use nullref\rbac\forms\RoleForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model RoleForm
 */

$this->title = Yii::t('rbac', 'Create role');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="role-create">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'List'), ['/rbac/role/index'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
        <div class="col-lg-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
