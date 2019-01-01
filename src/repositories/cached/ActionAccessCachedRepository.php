<?php

namespace nullref\rbac\repositories\cached;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\forms\ActionAccessForm;
use nullref\rbac\repositories\ActionAccessRepository;
use nullref\rbac\repositories\interfaces\ActionAccessRepositoryInterface;
use yii\caching\TagDependency;

class ActionAccessCachedRepository extends AbstractCachedRepository implements ActionAccessRepositoryInterface
{
    /** @var ActionAccessRepository */
    protected $repository;

    /**
     * ActionAccessCachedRepository constructor.
     *
     * @param ActionAccessRepository $repository
     */
    public function __construct(
        ActionAccessRepository $repository
    )
    {
        $this->repository = $repository;

        parent::__construct($repository);
    }

    public function findOneWithAuthItems($id)
    {
        $ar = $this->repository->getAr();
        $item = $ar::getDb()->cache(
            function () use ($id) {
                return $this->repository->findOneWithAuthItems($id);
            },
            null,
            new TagDependency(['tags' => $id . '-action'])
        );

        return $item;
    }

    public function findOneByMCA($module, $controller, $action)
    {
        $ar = $this->repository->getAr();
        $item = $ar::getDb()->cache(
            function () use ($module, $controller, $action) {
                return $this->repository->findOneByMCA($module, $controller, $action);
            },
            null,
            new TagDependency(['tags' => $module . '-' . $controller . '-' . $action . '-action'])
        );

        return $item;
    }

    public function updateWithItems(ActionAccessForm $form, ActionAccess $actionAccess)
    {
        $result = $this->repository->updateWithItems($form, $actionAccess);

        if ($result) {
            $this->invalidate($form->module . '-' . $form->controller . '-' . $form->action . '-action');
            $this->invalidate( $actionAccess->id . '-action');
        }

        return $result;
    }
}
