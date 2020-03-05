<?php

namespace nullref\rbac\repositories;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\ar\ActionAccessItem;
use nullref\rbac\forms\ActionAccessForm;
use nullref\rbac\repositories\interfaces\ActionAccessRepositoryInterface;

class ActionAccessRepository extends AbstractRepository implements ActionAccessRepositoryInterface
{
    /** @var ActionAccessItemRepository */
    private $aaiRepository;

    /**
     * ActionAccessRepository constructor.
     *
     * @param $activeRecord string
     * @param $actionAccessItemRepository ActionAccessItemRepository
     */
    public function __construct(
        ActionAccessItemRepository $actionAccessItemRepository,
        $activeRecord
    )
    {
        $this->aaiRepository = $actionAccessItemRepository;

        parent::__construct($activeRecord);
    }

    public function findOneWithAuthItems($id)
    {
        return $this->ar::find()
            ->andWhere(['id' => $id])
            ->with(['authItems'])
            ->one();
    }

    public function findOneByMCA($module, $controller, $action)
    {
        return $this->ar::find()
            ->with(['authItems'])
            ->where([
                'module'     => $module,
                'controller' => $controller,
                'action'     => $action,
            ])
            ->one();
    }

    public function assignItems($actionId, $items)
    {
        if (!is_array($items)) {
            $items = [];
        }

        $oldItems = $this->aaiRepository->findActionItems($actionId);

        //Add new items
        $itemsToAdd = [];
        foreach (array_diff($items, $oldItems) as $itemName) {
            $newItem = new ActionAccessItem([
                'action_access_id' => $actionId,
                'auth_item_name'   => $itemName,
            ]);
            $this->aaiRepository->save($newItem);
        }

        //Remove items
        $itemsToRemove = [];
        foreach (array_diff($oldItems, $items) as $itemName) {
            $itemsToRemove[] = $itemName;
        }

        $this->aaiRepository->delete(['auth_item_name' => $itemsToRemove, 'action_access_id' => $actionId]);

        return true;
    }

    public function saveWithItems(ActionAccessForm $form)
    {
        $actionAccess = new ActionAccess([
            'module'     => $form->module,
            'controller' => $form->controller,
            'action'     => $form->action,
        ]);
        if ($this->save($actionAccess)) {
            $this->assignItems($actionAccess->id, $form->items);

            return $actionAccess->id;
        }

        return false;
    }

    public function updateWithItems(ActionAccessForm $form, ActionAccess $actionAccess)
    {
        $actionAccess->module = $form->module;
        $actionAccess->controller = $form->controller;
        $actionAccess->action = $form->action;
        if ($this->save($actionAccess)) {
            $this->assignItems($actionAccess->id, $form->items);

            return $actionAccess->id;
        }

        return false;
    }
}
