<?php

namespace nullref\rbac\repositories;

class RoleRepository extends AbstractRepository
{
    protected $activeRecord;

    /** @var AuthItemChildRepository */
    private $authItemChildRepository;

    public function __construct(
        $activeRecord,
        AuthItemChildRepository $authItemChildRepository
    )
    {
        $this->ar = $activeRecord;
        $this->authItemChildRepository = $authItemChildRepository;
    }

    public function createWithParent($name, $parentName = '')
    {
        $role = new $this->ar;
        $role->name = $name;

        if ($parentName) {
            $this->addParent($name, $parentName);
        }

        return $role;
    }

    public function addParent($name, $parentName)
    {
        $relation = new $this->authItemChildRepository->ar();
        $relation->parent = $parentName;
        $relation->child = $name;

        return $this->authItemChildRepository->save($relation);
    }
}