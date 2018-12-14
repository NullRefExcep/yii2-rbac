<?php

namespace nullref\rbac\rules;

use yii\db\ActiveRecord;
use yii\rbac\Item;
use yii\rbac\Rule;

class AdminAffiliationRule extends Rule
{
    public $name = 'Affiliation of entity by admin';

    /**
     * @param string|int $userId the user ID.
     * @param Item $item the role or permission that this rule is associated width.
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     *
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($userId, $item, $params)
    {
        if (isset($params['entity'])) {
            /** @var ActiveRecord $entity */
            $entity = $params['entity'];
            $adminId = null;
            if (isset($entity->adminId)) {
                $adminId = ($entity->adminId) ?: null;
            }
            if (!$adminId && isset($entity->admin_id)) {
                $adminId = ($entity->admin_id) ?: null;
            }

            if ($userId == $adminId) {
                return true;
            }
        }

        return false;
    }
}