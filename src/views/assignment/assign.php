<?php

use nullref\rbac\assets\FancyTreeAsset;
use wbraganca\fancytree\FancytreeWidget;
use nullref\rbac\forms\AssignmentForm;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model AssignmentForm
 * @var $form ActiveForm
 * @var $tree array
 * @var $id integer
 * @var $username string
 */

FancyTreeAsset::register($this);
$this->registerJs(<<<JS
    var tree = jQuery("#fancyree_itemsTree");
    app.initTree = function () {
        tree.fancytree("getTree").generateFormElements("AssignmentForm[items][]");
    };
    app.selectTreeNode = app.initTree;
JS
);

$this->title = Yii::t('rbac', 'Assign');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assign">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) . ' ' . Yii::t('rbac', 'for') . ' ' . $username ?>
            </h1>
        </div>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-12">
            <?php $form->field($model, 'userId')->hiddenInput(['value' => $model->userId]) ?>

            <div class="row">
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
                <?= Html::submitButton(Yii::t('rbac', 'Assign'),
                    ['class' => 'btn btn-success']
                ) ?>
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
