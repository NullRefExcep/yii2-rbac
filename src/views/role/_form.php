<?php

use kartik\select2\Select2;
use nullref\rbac\forms\RoleForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model RoleForm
 */

?>

<div class="role-form">
    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'enableAjaxValidation'   => true,
    ]) ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'name') ?>

            <?= $form->field($model, 'description') ?>

            <?= $form->field($model, 'children')->widget(Select2::class, [
                'data'    => $model->getUnassignedItems(),
                'options' => [
                    'id'       => 'children',
                    'multiple' => true,
                ],
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rbac', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>
