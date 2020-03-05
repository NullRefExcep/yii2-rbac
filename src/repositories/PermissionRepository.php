<?php

namespace nullref\rbac\repositories;

use nullref\rbac\repositories\interfaces\AuthItemChildRepositoryInterface;

class PermissionRepository extends AbstractRepository
{
    protected $activeRecord;

    /** @var AuthItemChildRepositoryInterface */
    private $authItemChildRepository;

    public function __construct(
        $activeRecord,
        AuthItemChildRepositoryInterface $authItemChildRepository
    )
    {
        $this->ar = $activeRecord;
        $this->authItemChildRepository = $authItemChildRepository;
    }

    public function createWithParent($name, $parentName = '')
    {
        $permission = new $this->ar;
        $permission->name = $name;

        if ($parentName) {
            $this->addParent($name, $parentName);
        }

        return $permission;
    }

    public function addParent($name, $parentName)
    {
        $relation = new $this->authItemChildRepository->ar();
        $relation->parent = $parentName;
        $relation->child = $name;

        return $this->authItemChildRepository->save($relation);
    }
}
