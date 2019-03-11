<?php

namespace nullref\rbac\repositories;

use nullref\rbac\ar\FieldAccess;
use nullref\rbac\ar\FieldAccessItem;
use nullref\rbac\forms\FieldAccessForm;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use yii\helpers\ArrayHelper;

class FieldAccessRepository extends AbstractRepository implements FieldAccessRepositoryInterface
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

    public function findOneByMSA($model, $scenario, $attribute)
    {
        return $this->ar::find()
            ->with(['authItems'])
            ->where([
                'model_name'     => $model,
                'scenario_name'  => $scenario,
                'attribute_name' => $attribute,
            ])
            ->one();
    }

    public function findByMSAsArray($model, $scenario)
    {
        return $this->ar::find()
            ->with(['authItems'])
            ->where([
                'model_name'    => $model,
                'scenario_name' => $scenario,
            ])
            ->asArray()
            ->all();
    }

    public function findItems($model, $scenario, $attribute)
    {
        $field = $this->findOneByMSA($model, $scenario, $attribute);
        if ($field) {
            return $this->fieldAccessItemRepository->findItems($field);
        }

        return [];
    }

    public function findItemsForScenario($model, $scenario)
    {
        $scenarioFields = $this->findByMSAsArray($model, $scenario);
        if ($scenarioFields) {
            $items = [];
            foreach ($scenarioFields as $scenarioField) {
                if (!array_key_exists($scenarioField['attribute_name'], $items)) {
                    $items[$scenarioField['attribute_name']] = [];
                }
                $authItemNames = ArrayHelper::getColumn($scenarioField['authItems'], 'name');
                $items[$scenarioField['attribute_name']] = $authItemNames;
            }

            return $items;
        }

        return [];
    }

    public function assignItems($fieldId, $items)
    {
        if (!is_array($items)) {
            $items = [];
        }

        $oldItems = $this->fieldAccessItemRepository->findItems($fieldId);

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

    public function saveWithItems(FieldAccessForm $form)
    {
        $fieldAccess = new FieldAccess([
            'model_name'      => $form->modelName,
            'scenario_name'   => $form->scenarioName,
            'attribute_name'  => $form->attributeName,
            'description'     => $form->description,
            'permissions_map' => $form->permissionsMap,
        ]);
        if ($this->save($fieldAccess)) {
            $this->assignItems($fieldAccess->id, $form->items);

            return $fieldAccess->id;
        }

        return false;
    }

    public function updateWithItems(FieldAccessForm $form, FieldAccess $fieldAccess)
    {
        $fieldAccess->model_name = $form->modelName;
        $fieldAccess->scenario_name = $form->scenarioName;
        $fieldAccess->attribute_name = $form->attributeName;
        $fieldAccess->description = $form->description;
        $fieldAccess->permissions_map = $form->permissionsMap;
        if ($this->save($fieldAccess)) {
            $this->assignItems($fieldAccess->id, $form->items);

            return $fieldAccess->id;
        }

        return false;
    }

}