<?php

use nullref\rbac\forms\RuleForm;
use nullref\rbac\interfaces\RuleManagerInterface;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this  View
 * @var $model RuleForm
 * @var $ruleManager RuleManagerInterface
 */

?>

<div class="rule-form">

    <?php $form = ActiveForm::begin() ?>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->textInput() ?>

            <?= $form->field($model, 'class')->dropDownList($ruleManager->getList(), [
                'prompt' => Yii::t('rbac', 'Choose rule'),
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('rbac', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end() ?>
</div>
