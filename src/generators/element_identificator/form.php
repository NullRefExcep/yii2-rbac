<?php

use nullref\rbac\generators\element_identificator\Generator;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $form ActiveForm
 * @var $generator Generator
 */

?>

<div class="element-identificator-form">
    <?=
    $form->field($generator, 'aliases')
        ->checkboxList($generator->getViewPaths());
    ?>
</div>
