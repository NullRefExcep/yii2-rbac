<?php

namespace nullref\rbac\repositories;

class FieldAccessItemRepository extends AbstractRepository
{
    public function findActionItems($fieldId)
    {
        return $this->ar::find()
            ->select(['auth_item_name'])
            ->where(['field_access_id' => $fieldId])
            ->column();
    }
}