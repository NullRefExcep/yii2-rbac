<?php

namespace nullref\rbac\repositories\cached;

use nullref\rbac\ar\ElementAccess;
use nullref\rbac\forms\ElementAccessForm;
use nullref\rbac\repositories\ElementAccessRepository;
use nullref\rbac\repositories\interfaces\ElementAccessRepositoryInterface;
use yii\caching\TagDependency;

class ElementAccessCachedRepository extends AbstractCachedRepository implements ElementAccessRepositoryInterface
{
    /** @var ElementAccessRepository */
    protected $repository;

    /**
     * ElementAccessCachedRepository constructor.
     *
     * @param ElementAccessRepository $repository
     */
    public function __construct(
        ElementAccessRepository $repository
    )
    {
        $this->repository = $repository;

        parent::__construct($repository);
    }

    public function findItems($identifier)
    {
        $ar = $this->repository->getAr();
        $items = $ar::getDb()->cache(
            function () use ($identifier) {
                return $this->repository->findItems($identifier);
            },
            null,
            new TagDependency(['tags' => $identifier . '-element-items'])
        );

        return $items;
    }

    public function updateWithItems(ElementAccessForm $form, ElementAccess $elementAccess)
    {
        $result = $this->repository->updateWithItems($form, $elementAccess);

        if ($result) {
            $this->invalidate($elementAccess->identifier . '-element-items');
        }

        return $result;
    }
}
