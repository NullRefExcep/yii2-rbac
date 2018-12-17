<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

?>

<div class="element-config-modal-wrapper">
    <?php Modal::begin([
        'id'           => 'elementConfig',
        'size'         => Modal::SIZE_LARGE,
        'header'       => '<h3>' . $this->title . '</h3>',
        'toggleButton' => [
            'label' => Yii::t('rbac', 'Open element configuration'),
            'class' => 'btn btn-primary modal-control',
        ],
    ]); ?>

    <?php $form = ActiveForm::begin([
        'action' => '/rbac/element-access/save-ajax',
        'method' => 'post',
    ]) ?>

    <div class="row">
        <div class="hidden">
            <?= $form->field($model, 'type')->hiddenInput(['id' => 'element-type']) ?>
            <?= $form->field($model, 'identificator')->hiddenInput(['id' => 'element-identificator']) ?>
        </div>
        <div class="col-lg-12">
            <?= $form->field($model, 'description')->textarea(['id' => 'element-description']) ?>
        </div>
        <div class="col-lg-12">
            <?= $form->beginField($model, 'items') ?>
            <?= Html::activeLabel($model, 'items') ?>
            <?= FancytreeWidget::widget([
                'id'      => 'itemsTree',
                'options' => [
                    'source'          => $tree,
                    'checkbox'        => true,
                    'titlesTabbable'  => true,
                    'clickFolderMode' => 3,
                    'init'            => new JsExpression('app.initTree'),
                    'select'          => new JsExpression('app.selectTreeNode'),
                    'extensions'      => ["glyph", "edit", "wide"],
                    'activeVisible'   => true,
                    'autoCollapse'    => true,
                    'glyph'           => [
                        'map' => [
                            'doc'              => "fa fa-file-o",
                            'docOpen'          => "fa fa-file",
                            'checkbox'         => "fa fa-square-o",
                            'checkboxSelected' => "fa fa-check-square-o",
                            'checkboxUnknown'  => "fa fa-share",
                            'error'            => "fa fa-warning-sign",
                            'expanderClosed'   => "fa fa-plus-square-o",
                            'expanderLazy'     => "fa fa-spinner fa-spin",
                            'expanderOpen'     => "fa fa-minus-square-o",
                            'folder'           => "fa fa-folder-o",
                            'folderOpen'       => "fa fa-folder-open-o",
                            'loading'          => "fa fa-refresh",
                        ],
                    ],
                ],
            ]) ?>
            <?= $form->endField() ?>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-lg-12">
            <?= Html::submitButton(Yii::t('rbac', 'Confirm'), [
                'class'        => 'btn btn-success',
                'data-confirm' => Yii::t('rbac', 'Are you sure you want to set this works processed?'),
            ]); ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

    <?php Modal::end(); ?>
</div>
