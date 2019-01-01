<?php

namespace nullref\rbac\repositories;

class ElementAccessItemRepository extends AbstractRepository
{
    public function findItems($elementId)
    {
        return $this->ar::find()
            ->select(['auth_item_name'])
            ->where(['element_access_id' => $elementId])
            ->column();
    }
}