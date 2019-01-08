<?php

namespace nullref\rbac\repositories\cached;

use nullref\rbac\ar\FieldAccess;
use nullref\rbac\forms\FieldAccessForm;
use nullref\rbac\repositories\FieldAccessRepository;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use yii\caching\TagDependency;

class FieldAccessCachedRepository extends AbstractCachedRepository implements FieldAccessRepositoryInterface
{
    /** @var FieldAccessRepository */
    protected $repository;

    /**
     * FieldAccessCachedRepository constructor.
     *
     * @param FieldAccessRepository $repository
     */
    public function __construct(
        FieldAccessRepository $repository
    )
    {
        $this->repository = $repository;

        parent::__construct($repository);
    }

    public function findOneWithAuthItems($id)
    {
        $ar = $this->repository->getAr();
        $items = $ar::getDb()->cache(
            function () use ($id) {
                return $this->repository->findOneWithAuthItems($id);
            },
            null,
            new TagDependency(['tags' => $id . '-field'])
        );

        return $items;
    }

    public function findOneByMSA($model, $scenario, $attribute)
    {
        $ar = $this->repository->getAr();
        $items = $ar::getDb()->cache(
            function () use ($model, $scenario, $attribute) {
                return $this->repository->findOneByMSA($model, $scenario, $attribute);
            },
            null,
            new TagDependency(['tags' => $model . '-' . $scenario . '-' . $attribute . '-field'])
        );

        return $items;
    }

    public function findItems($model, $scenario, $attribute)
    {
        $ar = $this->repository->getAr();
        $items = $ar::getDb()->cache(
            function () use ($model, $scenario, $attribute) {
                return $this->repository->findItems($model, $scenario, $attribute);
            },
            null,
            new TagDependency(['tags' => $model . '-' . $scenario . '-' . $attribute . '-field-items'])
        );

        return $items;
    }

    public function findItemsForScenario($model, $scenario)
    {
        $ar = $this->repository->getAr();
        $items = $ar::getDb()->cache(
            function () use ($model, $scenario) {
                return $this->repository->findItemsForScenario($model, $scenario);
            },
            null,
            new TagDependency(['tags' => $model . '-' . $scenario . '-scenario-field-items'])
        );

        return $items;
    }

    public function updateWithItems(FieldAccessForm $form, FieldAccess $fieldAccess)
    {
        $result = $this->repository->updateWithItems($form, $fieldAccess);

        if ($result) {
            $this->invalidate(
                $fieldAccess->model_name . '-' .
                $fieldAccess->scenario_name . '-' .
                $fieldAccess->attribute_name . '-field-items'
            );
            $this->invalidate($fieldAccess->id . '-field-items');
            $this->invalidate(
                $fieldAccess->model_name . '-' .
                $fieldAccess->scenario_name . '-' .
                $fieldAccess->attribute_name . '-field'
            );
            $this->invalidate($fieldAccess->id . '-field');
            $this->invalidate(
                $fieldAccess->model_name . '-' .
                $fieldAccess->scenario_name . '-' . '-scenario-field-items'
            );
        }

        return $result;
    }

    public function delete($condition)
    {
        $model = $this->repository->findByCondition($condition);
        if ($model) {
            $this->invalidate(
                $model->model . '-' .
                $model->scenario . '-' .
                $model->attribute . '-field-items'
            );
            $this->invalidate($model->id . '-field-items');
            $this->invalidate(
                $model->model . '-' .
                $model->scenario . '-' .
                $model->attribute . '-field'
            );
            $this->invalidate($model->id . '-field');
            $this->invalidate(
                $model->model_name . '-' .
                $model->scenario_name . '-' . '-scenario-field-items'
            );
        }
        $this->repository->delete($condition);
    }
}
