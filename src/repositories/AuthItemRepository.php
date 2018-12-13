<?php

namespace nullref\rbac\repositories;

use nullref\rbac\components\DBManager;
use yii\helpers\ArrayHelper;

class AuthItemRepository extends AbstractRepository
{
    /** @var DBManager */
    private $manager;

    /**
     * AuthItemRepository constructor.
     *
     * @param $activeRecord
     * @param DBManager $manager
     */
    public function __construct(
        $activeRecord,
        DBManager $manager
    )
    {
        $this->manager = $manager;

        parent::__construct($activeRecord);
    }

    public function getChildByName($name)
    {
        return $this->ar::find()->andWhere(['child' => $name])->one();
    }

    public function getMapByType($type, $keyField, $valueField, $condition = [], $asArray = true)
    {
        $query = $this->ar::find()
            ->andWhere(['type' => $type]);
        if (!empty($condition)) {
            $query->where($condition);
        }
        if ($asArray) {
            $query->asArray();
        }

        return ArrayHelper::map($query->all(), $keyField, $valueField);
    }

    /**
     * @param $item
     * @param null $type
     *
     * @return array
     */
    public function getUnassignedItems($item, $type = null)
    {
        $data = [];
        $items = $this->manager->getItems(
            $type,
            $item !== null ? [$item->name] : []
        );

        if ($item === null) {
            foreach ($items as $itemEntry) {
                $data[$itemEntry->name] = $this->itemName($itemEntry);
            }
        } else {
            foreach ($items as $child) {
                if ($this->manager->canAddChild($item, $child)) {
                    $data[$child->name] = $this->itemName($child);
                }
            }
        }

        return $data;
    }

    /**
     * @param $item Role
     *
     * @return string
     */
    private function itemName($item)
    {
        return empty($item->description)
            ? $item->name
            : $item->name . ' (' . $item->description . ')';
    }
}