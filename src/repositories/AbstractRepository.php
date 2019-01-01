<?php

namespace nullref\rbac\repositories;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

abstract class AbstractRepository
{
    /** @var string */
    protected $ar;

    public function __construct($activeRecord)
    {
        $this->ar = $activeRecord;
    }

    public function getAr()
    {
        return $this->ar;
    }

    public function findOne($id)
    {
        return $this->ar::find()
            ->andWhere(['id' => $id])
            ->one();
    }

    public function findAll()
    {
        return $this->ar::find()
            ->all();
    }

    /**
     * @return ActiveRecord
     */
    public function findOneByCondition($condition)
    {
        return $this->ar::find()
            ->andWhere($condition)
            ->one();
    }

    /**
     * @return ActiveRecord[]
     */
    public function findByCondition($condition)
    {
        return $this->ar::find()
            ->andWhere($condition)
            ->all();
    }

    public function getMap($keyField, $valueField, $condition = [], $asArray = true)
    {
        $query = $this->ar::find();
        if (!empty($condition)) {
            $query->where($condition);
        }
        if ($asArray) {
            $query->asArray();
        }

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }

    /**
     * @return ActiveRecord
     */
    public function save($model)
    {
        if ($model instanceof ActiveRecord) {
            return $model->save();
        } elseif (is_array($model)) {
            $instance = new $this->ar();
            $instance->load($model);

            return $instance->save();
        } else {
            return false;
        }
    }

    /**
     * @param $condition
     *
     * @return void
     */
    public function delete($condition)
    {
        $this->ar::deleteAll($condition);
    }
}