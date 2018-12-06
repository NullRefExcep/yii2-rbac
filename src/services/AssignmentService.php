<?php

namespace nullref\rbac\services;

class AssignmentService
{
    /** @var DbManager */
    private $manager;

    /** @var AuthAssignmentRepository */
    private $repository;

    /**
     * AssignmentService constructor.
     *
     * @param DbManager $manager
     * @param AuthAssignmentRepository $repository
     */
    public function __construct(
        DbManager $manager,
        AuthAssignmentRepository $repository
    )
    {
        $this->manager = $manager;
        $this->repository = $repository;

        parent::__construct();
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getUserAssignments(int $userId)
    {
        return array_keys($this->manager->getItemsByUser($userId));
    }

    /**
     * @param int $userId
     * @param array $items
     *
     * @return bool
     */
    public function updateAssignments(int $userId, array $items = [])
    {
        return $this->repository->updateAssignments($userId, $items);
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