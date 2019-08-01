<?php

namespace nullref\rbac\search;

use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/**
 * Class AssignmentSearch
 * @package nullref\rbac\search
 */
class AssignmentSearch
{
    /** @var array */
    private $users = [];

    /** @var array */
    private $properties = [];

    /**
     * @param $columns
     */
    public function setColumns($columns)
    {
        foreach ($columns as $column) {
            $property = $column['attribute'];
            $this->{$property} = '';
            $this->properties[] = $property;
        }
    }

    /**
     * @param $columns
     *
     * @return mixed
     */
    public function processColumns($columns)
    {
        foreach ($columns as $key => $column) {
            $attributeName = $column['attribute'];
            $columns[$key]['filter'] = Html::textInput(
                $attributeName,
                $this->{$attributeName},
                ['class' => 'form-control',]
            );
        }

        return $columns;
    }

    /**
     * @param $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @param $params
     *
     * @return ArrayDataProvider
     */
    public function search($params)
    {
        $users = $this->users;

        $this->load($params);

        foreach ($this->properties as $property) {
            if ($this->{$property}) {
                $users = array_filter($users, function ($item) use ($property) {
                    if (strpos($item[$property], $this->{$property}) !== false) {
                        return $item;
                    }
                });
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels'  => $users,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        return $dataProvider;
    }

    private function load($values)
    {
        foreach ($values as $attribute => $value) {
            if (isset($this->{$attribute}) && $value) {
                $this->{$attribute} = $value;
            }
        }
    }
}
