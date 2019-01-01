<?php

namespace nullref\rbac\services;

use nullref\rbac\components\DBManager;
use nullref\rbac\repositories\interfaces\AuthAssignmentRepositoryInterface;
use yii\helpers\ArrayHelper;

class AssignmentService
{
    /** @var DBManager */
    private $manager;

    /** @var AuthAssignmentRepositoryInterface */
    private $authAssignmentRepository;

    /**
     * AssignmentService constructor.
     *
     * @param DBManager $manager
     * @param AuthAssignmentRepositoryInterface $authAssignmentRepository
     */
    public function __construct(
        DBManager $manager,
        AuthAssignmentRepositoryInterface $authAssignmentRepository
    )
    {
        $this->manager = $manager;
        $this->authAssignmentRepository = $authAssignmentRepository;
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getUserAssignments(int $userId)
    {
        return array_keys($this->authAssignmentRepository->getUserAssignments($userId));
    }

    /**
     * @param int $userId
     * @param array $items
     *
     * @return bool
     */
    public function updateAssignments(int $userId, array $items = [])
    {
        return $this->authAssignmentRepository->updateAssignments($userId, $items);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return ArrayHelper::map(
            $this->manager->getItems(),
            'name',
            function ($item) {
                return empty($item->description)
                    ? $item->name
                    : $item->name . ' (' . $item->description . ')';
            }
        );
    }
}