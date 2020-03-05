<?php

namespace nullref\rbac\repositories;

use nullref\rbac\components\DBManager;
use nullref\rbac\repositories\interfaces\AuthItemChildRepositoryInterface;
use yii\helpers\ArrayHelper;

class AuthItemChildRepository extends AbstractRepository implements AuthItemChildRepositoryInterface
{
    public function getChildByName($name)
    {
        return $this->ar::find()->andWhere(['child' => $name])->one();
    }

    public function addParent($name, $parentName)
    {
        $relation = new $this->ar();
        $relation->parent = $parentName;
        $relation->child = $name;

        return $this->save($relation);
    }

}
