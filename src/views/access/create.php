<?php

use nullref\rbac\forms\ActionAccessForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model ActionAccessForm
 * @var $modules array
 * @var $tree array
 */

$this->title = Yii::t('rbac', 'Create Action Access');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Action Accesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="action-access-create">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'List'), ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= $this->render('_form', [
        'model'   => $model,
        'modules' => $modules,
        'tree'    => $tree,
        'isNew'   => true,
    ]) ?>

</div>
