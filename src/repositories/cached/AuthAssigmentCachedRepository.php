<?php

namespace nullref\rbac\repositories\cached;

use nullref\rbac\repositories\AuthAssignmentRepository;
use nullref\rbac\repositories\interfaces\AuthAssignmentRepositoryInterface;
use yii\caching\TagDependency;

class AuthAssigmentCachedRepository extends AbstractCachedRepository implements AuthAssignmentRepositoryInterface
{
    /** @var AuthAssignmentRepository */
    protected $repository;

    /**
     * AuthAssigmentCachedRepository constructor.
     *
     * @param AuthAssignmentRepository $repository
     */
    public function __construct(
        AuthAssignmentRepository $repository
    )
    {
        $this->repository = $repository;

        parent::__construct($repository);
    }

    public function getUserAssignments($userId)
    {
        $ar = $this->repository->getAr();
        $items = $ar::getDb()->cache(
            function () use ($userId) {
                return $this->repository->getUserAssignments($userId);
            },
            null,
            new TagDependency(['tags' => $userId . '-user-items'])
        );

        return $items;
    }

    public function updateAssignments($userId, $items)
    {
        $result = $this->repository->updateAssignments($userId, $items);

        if ($result) {
            $this->invalidate( $userId . '-user-items');
        }

        return $result;
    }
}
