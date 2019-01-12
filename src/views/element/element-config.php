<?php

use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $items array
 * @var $selected array
 */

$this->registerJs(<<<JS
    app.selection = $selected;
JS
);

?>

<div class="element-config-modal-wrapper">
    <?php Modal::begin([
        'id'           => 'elementConfig',
        'size'         => Modal::SIZE_LARGE,
        'header'       => '<h3>' . $this->title . '</h3>',
    ]); ?>

    <?php $form = ActiveForm::begin([
        'id'     => 'elementConfigForm',
        'action' => '/rbac/element/save-ajax',
        'method' => 'post',
    ]) ?>

    <div class="row">
        <div class="hidden">
            <?= $form->field($model, 'identifier')->hiddenInput(['id' => 'elementIdentifier']) ?>
        </div>
        <div class="col-lg-12">
            <?= $form->field($model, 'description')->textarea(['id' => 'element-description']) ?>
        </div>
        <div class="col-lg-12">
            <?= $form->field($model, 'items')->widget(Select2::class, [
                'data'    => $items,
                'options' => [
                    'id'          => 'elementItems',
                    'placeholder' => Yii::t('rbac', 'Select items'),
                    'multiple'    => true,
                ],
            ]) ?>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-lg-12">
            <?= Html::submitButton(Yii::t('rbac', 'Save'), [
                'class'        => 'btn btn-success',
                'data-confirm' => Yii::t('rbac', 'Are you sure you want to save it?'),
            ]); ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

    <?php Modal::end(); ?>
</div>
