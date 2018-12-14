<?php

namespace nullref\rbac\rules;

use yii\db\ActiveRecord;
use yii\rbac\Item;
use yii\rbac\Rule;

class UserAffiliationRule extends Rule
{
    public $name = 'Affiliation of entity by user';

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
            $entityUserId = null;
            if (isset($entity->userId)) {
                $entityUserId = ($entity->userId) ?: null;
            }
            if (!$entityUserId && isset($entity->user_id)) {
                $entityUserId = ($entity->user_id) ?: null;
            }

            if ($userId == $entityUserId) {
                return true;
            }
        }

        return false;
    }
}