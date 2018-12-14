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
            $query->orWhere(['LIKE', 'LOWER(name)', mb_strtolower($searchQuery)]);
            $query->orWhere(['LIKE', 'name', '%' . $searchQuery]);
            $query->orWhere(['LIKE', 'name', $searchQuery . '%']);
            $query->orWhere(['LIKE', 'name', '%' . $searchQuery . '%']);
        }

        return $query->all();
    }
}