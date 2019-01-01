<?php

namespace nullref\rbac\repositories;

use nullref\rbac\components\DBManager;
use nullref\rbac\repositories\interfaces\AuthAssignmentRepositoryInterface;

class AuthAssignmentRepository extends AbstractRepository implements AuthAssignmentRepositoryInterface
{
    /** @var DBManager */
    private $manager;

    public function __construct(
        DBManager $manager,
        $activeRecord
    )
    {
        $this->manager = $manager;

        parent::__construct($activeRecord);
    }

    public function getUserAssignments($userId)
    {
        return $this->manager->getItemsByUserId($userId);
    }

    public function updateAssignments($userId, $items)
    {

        if (!is_array($items)) {
            $items = [];
        }

        $assignedItems = $this->manager->getItemsByUserId($userId);
        $assignedItemsNames = array_keys($assignedItems);

        foreach (array_diff($assignedItemsNames, $items) as $item) {
            $this->manager->revoke($assignedItems[$item], $userId);
        }

        foreach (array_diff($items, $assignedItemsNames) as $item) {
            $this->manager->assign($this->manager->getItem($item), $userId);
        }

        $this->updated = true;

        return true;
    }
}