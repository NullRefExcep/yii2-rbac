<?php

namespace nullref\rbac\validators;

use nullref\rbac\components\DBManager;
use yii\validators\Validator;
use Yii;

class ItemsValidator extends Validator
{
    /** @var DBManager */
    protected $manager;

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->manager = Yii::$app->authManager;
    }

    /** @inheritdoc */
    protected function validateValue($value)
    {
        if (!is_array($value)) {
            return [Yii::t('rbac', 'Invalid value'), []];
        }

        foreach ($value as $val) {
            if ($this->manager->getItem($val) == null) {
                return [Yii::t('rbac', 'There is neither role nor permission with name "{0}"', [$val]), []];
            }
        }
    }
}