<?php

use kartik\select2\Select2;
use nullref\rbac\forms\PermissionForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this  View
 * @var $model PermissionForm
 */

?>

<div class="permission-form">
    <?php $form = ActiveForm::begin([]) ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'description')->textarea() ?>

    <?= $form->field($model, 'rule')->widget(Select2::class, [
        'options'       => [
            'placeholder' => Yii::t('rbac', 'Select rule'),
        ],
        'pluginOptions' => [
            'ajax'       => [
                'url'  => Url::to(['/rbac/rule/search']),
                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
            ],
            'allowClear' => true,
        ],
    ]) ?>

    <?php if ($model->dataCannotBeDecoded): ?>
        <div class="alert alert-info">
            <?= Yii::t('rbac', 'Data cannot be decoded') ?>
        </div>
    <?php else: ?>
        <?= $form->field($model, 'data')->textInput() ?>
    <?php endif ?>

    <?= $form->field($model, 'children')->widget(Select2::class, [
        'data'    => $model->getUnassignedItems(),
        'options' => [
            'id'       => 'children',
            'multiple' => true,
        ],
    ]) ?>

    <?= Html::submitButton(Yii::t('rbac', 'Save'), ['class' => 'btn btn-success btn-block']) ?>

    <?php ActiveForm::end() ?>
</div>
    