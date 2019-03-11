<?php

use kartik\depdrop\DepDrop;
use nullref\rbac\assets\FancyTreeAsset;
use nullref\rbac\enum\PermissionsMap;
use nullref\rbac\forms\FieldAccessForm;
use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model FieldAccessForm
 * @var $form ActiveForm
 * @var $models array
 * @var $tree array
 * @var $isNew bool
 */

FancyTreeAsset::register($this);
$this->registerJs(<<<JS
    var tree = jQuery("#fancyree_itemsTree");
    app.initTree = function () {
        tree.fancytree("getTree").generateFormElements("FieldAccessForm[items][]");
    };
    app.selectTreeNode = app.initTree;
JS

);

?>

<div class="field-access-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'modelName')->dropDownList($models, [
                'id'     => 'modelName',
                'prompt' => Yii::t('rbac', 'Choose model'),
            ]) ?>

            <?= $form->field($model, 'scenarioName')->widget(DepDrop::class, [
                'options'       => ['id' => 'scenarioName'],
                'value'         => $model->scenarioName,
                'pluginOptions' => [
                    'depends'     => ['modelName'],
                    'placeholder' => Yii::t('rbac', 'Choose scenario'),
                    'url'         => Url::to([
                        '/rbac/field/scenarios',
                        'selected' => $model->scenarioName,
                    ]),
                    'initialize'  => !$isNew,
                ],
            ]); ?>

            <?= $form->field($model, 'attributeName')->widget(DepDrop::class, [
                'options'       => ['id' => 'field'],
                'value'         => $model->attributeName,
                'pluginOptions' => [
                    'depends'     => ['modelName', 'scenarioName'],
                    'placeholder' => Yii::t('rbac', 'Choose field'),
                    'url'         => Url::to([
                        '/rbac/field/attributes',
                        'selected' => $model->attributeName,
                    ]),
                    'initialize'  => !$isNew,
                ],
            ]); ?>

            <?= $form->field($model, 'description')->textarea() ?>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <?=
            $form->field($model, 'permissionsMap')
                ->checkboxList(PermissionsMap::getPermissions())
            ?>

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

    <div class="form-group">
        <?= Html::submitButton(
            $isNew ? Yii::t('rbac', 'Create') : Yii::t('rbac', 'Update'),
            [
                'class' => $isNew ? 'btn btn-success' : 'btn btn-primary',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
