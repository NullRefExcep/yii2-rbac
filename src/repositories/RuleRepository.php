<?php

namespace nullref\rbac\repositories;

class RuleRepository extends AbstractRepository
{
    public function getRuleNames($searchQuery = null)
    {
        $query = $this->ar::find()
            ->select(['id' => 'name', 'text' => 'name'])
            ->from($this->ar::tableName())
            ->orderBy(['name' => SORT_ASC])
            ->limit(10);

        if ($searchQuery) {
            $query->where(['LIKE', 'LOWER(name)', mb_strtolower($searchQuery)]);
        }

        return $query->all();
    }
}