<?php

namespace nullref\rbac\repositories\cached;

use nullref\rbac\repositories\AbstractRepository;
use Yii;
use yii\caching\TagDependency;

abstract class AbstractCachedRepository
{
    protected $cache;

    protected $repository;

    /**
     * AbstractCachedRepository constructor.
     *
     * @param AbstractRepository $repository
     */
    public function __construct(AbstractRepository $repository)
    {
        $this->cache = Yii::$app->cache;
        $this->repository = $repository;
    }

    public function __call($name, $arguments)
    {
        //Call repository method
        if (method_exists($this->repository, $name)) {
            return call_user_func_array([$this->repository, $name], $arguments);
        }

        return null;
    }

    public function invalidate($tags)
    {
        TagDependency::invalidate($this->cache, $tags);
    }
}
