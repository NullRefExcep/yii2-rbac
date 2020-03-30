<?php

namespace nullref\rbac\facades;

use nullref\rbac\services\AssignmentService;
use nullref\rbac\services\AuthTreeService;
use yii\base\Component;

class RBACFacade extends Component
{
    /** @var array */
    private $tree = null;

    /** @var AssignmentService */
    private $assignmentService;

    /** @var AuthTreeService */
    private $authTree;

    /**
     * RBACFacade constructor.
     *
     * @param AssignmentService $assignmentService
     * @param AuthTreeService $authTree
     */
    public function __construct(
        AssignmentService $assignmentService,
        AuthTreeService $authTree
    )
    {
        $this->assignmentService = $assignmentService;
        $this->authTree = $authTree;
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getUserAssignments(int $userId)
    {
        return $this->assignmentService->getUserAssignments($userId);
    }

    /**
     * @param int $userId
     * @param array $items
     *
     * @return bool
     */
    public function updateAssignments(int $userId, array $items = [])
    {
        return $this->assignmentService->updateAssignments($userId, $items);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->assignmentService->getItems();
    }

    /**
     * @return array
     */
    public function getTree()
    {
        if ($this->tree === null) {
            $this->tree = $this->authTree->getArrayAuthTree(
                $this->authTree->getAuthTree()
            );
        }

        return $this->tree;
    }
}
