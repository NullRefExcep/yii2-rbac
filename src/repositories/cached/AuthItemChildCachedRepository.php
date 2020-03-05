<?php

namespace nullref\rbac\repositories\cached;

use nullref\rbac\repositories\AuthItemChildRepository;
use nullref\rbac\repositories\interfaces\AuthItemChildRepositoryInterface;
use yii\caching\TagDependency;
use yii\db\Connection;

class AuthItemChildCachedRepository extends AbstractCachedRepository implements AuthItemChildRepositoryInterface
{
    /** @var AuthItemChildRepository */
    protected $repository;

    /**
     * AuthItemCachedRepository constructor.
     *
     * @param AuthItemChildRepository $repository
     */
    public function __construct(
        AuthItemChildRepository $repository
    )
    {
        $this->repository = $repository;

        parent::__construct($repository);
    }

    /**
     * @param $model
     *
     * @return \yii\db\ActiveRecord|null
     */
    public function save($model)
    {
        $result = $this->repository->save($model);

        if ($result) {
            //Invalidate all RBAC
            $this->invalidateRBAC();
        }

        return $result;
    }

    /**
     * @param $condition
     *
     * @return void
     */
    public function delete($condition)
    {
        $this->repository->delete($condition);
        $this->invalidateRBAC();
    }

    private function invalidateRBAC()
    {
        $tags = [];
        try {
            /** @var Connection $db */
            $db = $this->repository->getAr()::getDb();
            $module = \Yii::$app->getModule('rbac');
            $userIds = $db->createCommand("SELECT id FROM $module->userTable;")->queryAll();
        } catch (\Exception $e) {
            $userIds = [];
        }
        foreach ($userIds as $user) {
            $tags[] = $user['id'] . '-user-items';
        }

        if (!empty($tags)) {
            $this->invalidate($tags);
        }
    }
}
