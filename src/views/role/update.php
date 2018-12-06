<?php

use nullref\rbac\forms\RoleForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $model RoleForm
 * @var $this View
 */

$this->title = Yii::t('rbac', 'Update role');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="role-update">
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

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
