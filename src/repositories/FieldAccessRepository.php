<?php

namespace nullref\rbac\repositories;

use nullref\rbac\ar\FieldAccessItem;

class FieldAccessRepository extends AbstractRepository
{
    /** @var FieldAccessItemRepository */
    private $fieldAccessItemRepository;

    /**
     * FieldAccessRepository constructor.
     *
     * @param $activeRecord string
     * @param FieldAccessItemRepository $fieldAccessItemRepository
     */
    public function __construct(
        FieldAccessItemRepository $fieldAccessItemRepository,
        $activeRecord
    )
    {
        $this->fieldAccessItemRepository = $fieldAccessItemRepository;

        parent::__construct($activeRecord);
    }

    public function findOneWithAuthItems($id)
    {
        return $this->ar::find()
            ->andWhere(['id' => $id])
            ->with(['authItems'])
            ->one();
    }

    public function assignItems($fieldId, $items)
    {
        if (!is_array($items)) {
            $items = [];
        }

        $oldItems = $this->fieldAccessItemRepository->findActionItems($fieldId);

        //Add new items
        foreach (array_diff($items, $oldItems) as $itemName) {
            $newItem = new FieldAccessItem([
                'field_access_id' => $fieldId,
                'auth_item_name'  => $itemName,
            ]);
            $this->fieldAccessItemRepository->save($newItem);
        }

        //Remove items
        $itemsToRemove = [];
        foreach (array_diff($oldItems, $items) as $itemName) {
            $itemsToRemove[] = $itemName;
        }

        $this->fieldAccessItemRepository->delete([
            'auth_item_name'  => $itemsToRemove,
            'field_access_id' => $fieldId,
        ]);

        return true;
    }


}