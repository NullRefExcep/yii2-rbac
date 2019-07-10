<?php

namespace nullref\rbac\components;

use nullref\rbac\interfaces\RuleManagerInterface;
use nullref\rbac\rules\AdminAffiliationRule;
use nullref\rbac\rules\UserAffiliationRule;
use yii\base\Component;

class RuleManager extends Component implements RuleManagerInterface
{
    public function getList()
    {
        return [
            AdminAffiliationRule::class => 'AdminAffiliationRule',
            UserAffiliationRule::class  => 'UserAffiliationRule',
        ];
    }
}
