<?php

use nullref\rbac\assets\TreeAsset;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $tree array
 */

$this->title = Yii::t('rbac', 'Auth items');
$this->params['breadcrumbs'][] = $this->title;

TreeAsset::register($this);

?>

<div class="auth-item-index">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <div class="tree" id="treeView" data-form-name="tree">
        <?php if (count($tree)): ?>
            <ol class="auth-list">
                <?php foreach ($tree as $node): ?>
                    <?= $this->render('_item', [
                        'node' => $node,
                    ]) ?>
                <?php endforeach; ?>
            </ol>
        <?php endif ?>
    </div>
</div>