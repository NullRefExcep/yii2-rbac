<?php

use nullref\rbac\forms\RuleForm;
use nullref\rbac\interfaces\RuleManagerInterface;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this  View
 * @var $model RuleForm
 * @var $ruleManager RuleManagerInterface
 */

$this->title = Yii::t('rbac', 'Update rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="role-update">

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
    </div>

    <p>
        <?= Html::a(Yii::t('rbac', 'List'), ['/rbac/rule/index'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
        <div class="col-lg-12">
            <?= $this->render('_form', [
                'model'       => $model,
                'ruleManager' => $ruleManager,
            ]) ?>
        </div>
    </div>

</div>