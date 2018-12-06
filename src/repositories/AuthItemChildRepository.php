<?php

namespace nullref\rbac\repositories;

use nullref\rbac\components\DbManager;
use yii\helpers\ArrayHelper;

class AuthItemChildRepository extends AbstractRepository
{
    public function getChildByName($name)
    {
        return $this->ar::find()->andWhere(['child' => $name])->one();
    }

}