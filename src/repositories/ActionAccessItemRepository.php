<?php

namespace nullref\rbac\repositories;

class ActionAccessItemRepository extends AbstractRepository
{
    public function findActionItems($actionId)
    {
        return $this->ar::find()
            ->select(['auth_item_name'])
            ->where(['action_access_id' => $actionId])
            ->column();
    }
}