<?php

namespace nullref\rbac\widgets;

use nullref\rbac\services\FieldCheckerService;
use yii\widgets\ActiveField as BaseActiveField;
use Yii;

class ActiveField extends BaseActiveField
{
    /** @var FieldCheckerService */
    private $fieldCheckerService;

    public function init()
    {
        $this->fieldCheckerService = Yii::$container->get(FieldCheckerService::class);

        parent::init();
    }

    public function render($content = null)
    {
        $result = parent::render($content);
        if (!$this->fieldCheckerService->isAllowed($this->model, $this->attribute)) {
            $result = '';
        }

        return $result;
    }
}