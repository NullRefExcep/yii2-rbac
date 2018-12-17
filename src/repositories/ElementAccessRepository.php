<?php

namespace nullref\rbac\repositories;

use nullref\rbac\ar\ElementAccess;
use nullref\rbac\ar\ElementAccessItem;
use nullref\rbac\forms\ElementAccessForm;

class ElementAccessRepository extends AbstractRepository
{
    /** @var ElementAccessRepository */
    private $elementAccessItemRepository;

    /**
     * ElementAccessRepository constructor.
     *
     * @param $activeRecord string
     * @param ElementAccessItemRepository $elementAccessItemRepository
     */
    public function __construct(
        ElementAccessItemRepository $elementAccessItemRepository,
        $activeRecord
    )
    {
        $this->elementAccessItemRepository = $elementAccessItemRepository;

        parent::__construct($activeRecord);
    }

    public function findOneWithAuthItems($id)
    {
        return $this->ar::find()
            ->andWhere(['id' => $id])
            ->with(['authItems'])
            ->one();
    }

    public function findItems($identificator)
    {
        $element = $this->findOneByCondition(['identificator' => $identificator]);
        if ($element) {
            $this->elementAccessItemRepository->findActionItems($element->id);
        }

        return [];
    }

    public function assignItems($elementId, $items)
    {
        if (!is_array($items)) {
            $items = [];
        }

        $oldItems = $this->elementAccessItemRepository->findActionItems($elementId);

        //Add new items
        foreach (array_diff($items, $oldItems) as $itemName) {
            $newItem = new ElementAccessItem([
                'element_access_id' => $elementId,
                'auth_item_name'    => $itemName,
            ]);
            $this->elementAccessItemRepository->save($newItem);
        }

        //Remove items
        $itemsToRemove = [];
        foreach (array_diff($oldItems, $items) as $itemName) {
            $itemsToRemove[] = $itemName;
        }

        $this->elementAccessItemRepository->delete([
            'auth_item_name'    => $itemsToRemove,
            'element_access_id' => $elementId,
        ]);

        return true;
    }

    public function saveWithItems(ElementAccessForm $form)
    {
        $elementAccess = new ElementAccess([
            'type'          => $form->type,
            'identificator' => $form->identificator,
            'description'   => $form->description,
        ]);
        if ($this->save($elementAccess)) {
            $this->assignItems($elementAccess->id, $form->items);

            return $elementAccess->id;
        }

        return false;
    }

    public function updateWithItems(ElementAccessForm $form, ElementAccess $elementAccess)
    {
        $elementAccess->type = $form->type;
        $elementAccess->identificator = $form->identificator;
        $elementAccess->description = $form->description;
        if ($this->save($elementAccess)) {
            $this->assignItems($elementAccess->id, $form->items);

            return $elementAccess->id;
        }

        return false;
    }

}
