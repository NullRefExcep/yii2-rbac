<?php

namespace nullref\rbac\services;

use nullref\rbac\components\DBManager;
use nullref\rbac\Module;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use Yii;
use yii\base\Model;
use yii\web\User;

class FieldCheckerService
{
    /** @var DBManager */
    private $manager;

    /** @var FieldAccessRepositoryInterface */
    private $fieldAccessRepository;

    /** @var User|null */
    private $userIdentity;

    public function __construct(
        DBManager $manager,
        FieldAccessRepositoryInterface $fieldAccessRepository
    )
    {
        $this->manager = $manager;
        $this->fieldAccessRepository = $fieldAccessRepository;

        /** @var Module $module */
        $module = Yii::$app->getModule('rbac');
        $this->userIdentity = $module->getUserIdentity();
    }

    /**
     * @param $model - object of model of model class
     * @param $attribute - name of model attribute
     * @return bool
     */
    public function isAllowed($model, $attribute)
    {
        $identity = $this->userIdentity;
        if ($identity) {
            $userId = $identity->getId();
            $className = is_object($model) ? get_class($model) : $model;
            $scenario = is_object($model) ? $model->scenario :  Model::SCENARIO_DEFAULT;
            $fieldItems = $this->fieldAccessRepository->findItems($className, $scenario, $attribute);
            if (empty($fieldItems)) {
                return true;
            }

            foreach ($fieldItems as $fItem) {
                if ($this->manager->checkAccess($userId, $fItem)) {
                    return true;
                }
            }
        }

        return false;
    }
}
