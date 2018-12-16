<?php

use nullref\rbac\forms\ElementAccessForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var $model ElementAccessForm
 * @var $modules array
 * @var $tree array
 * @var $types array
 */

$this->title = Yii::t('rbac', 'Create Element Access');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Element Accesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="element-access-create">

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
        'model' => $model,
        'tree'  => $tree,
        'types' => $types,
        'isNew' => true,
    ]) ?>

</div>
