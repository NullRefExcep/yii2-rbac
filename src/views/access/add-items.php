<?php

use nullref\rbac\models\ActionAccess;
use nullref\rbac\widgets\ActionItems;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $id integer
 * @var $actionAccess ActionAccess
 * @var $authItemRepository AuthItemRepository
 */

$this->title = Yii::t('rbac', 'Add items to action');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="action-index">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h4>
                <?= $actionAccess->module .' / '. $actionAccess->controller .' / '. $actionAccess->action ?>
            </h4>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'List'), ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= ActionItems::widget(['actionId' => $id, 'repository' => $authItemRepository]) ?>

</div>
