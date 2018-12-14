<?php

use kartik\depdrop\DepDrop;
use nullref\rbac\assets\FancyTreeAsset;
use nullref\rbac\forms\ActionAccessForm;
use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model ActionAccessForm
 * @var $form ActiveForm
 * @var $modules array
 * @var $tree array
 * @var $isNew bool
 */

FancyTreeAsset::register($this);
$this->registerJs(<<<JS
    var tree = jQuery("#fancyree_itemsTree");
    app.initTree = function () {
        tree.fancytree("getTree").generateFormElements("ActionAccessForm[items][]");
    };
    app.selectTreeNode = app.initTree;
JS

);

?>

<div class="action-access-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'module')->dropDownList($modules, [
                'prompt' => Yii::t('rbac', 'Choose module'),
            ]) ?>

            <?= $form->field($model, 'controller')->widget(DepDrop::class, [
                'options'       => ['id' => 'controller'],
                'value'         => $model->controller,
                'pluginOptions' => [
                    'depends'     => [Html::getInputId($model, 'module')],
                    'placeholder' => Yii::t('rbac', 'Choose controller'),
                    'url'         => Url::to([
                        '/rbac/access/controllers',
                        'selected' => $model->controller,
                    ]),
                    'initialize'  => !$isNew,
                ],
            ]); ?>

            <?= $form->field($model, 'action')->widget(DepDrop::class, [
                'options'       => ['id' => 'action'],
                'value'         => $model->action,
                'pluginOptions' => [
                    'depends'     => [Html::getInputId($model, 'module'), 'controller'],
                    'placeholder' => Yii::t('rbac', 'Choose action'),
                    'url'         => Url::to([
                        '/rbac/access/actions',
                        'selected' => $model->action,
                    ]),
                    'initialize'  => !$isNew,
                ],
            ]); ?>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
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
