<?php

use nullref\rbac\forms\FieldAccessForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model FieldAccessForm
 * @var $models array
 * @var $tree array
 */

$this->title = Yii::t('rbac', 'Create Field Access');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Field Accesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-access-create">

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
        'model'  => $model,
        'models' => $models,
        'tree'   => $tree,
        'isNew'  => true,
    ]) ?>

</div>
