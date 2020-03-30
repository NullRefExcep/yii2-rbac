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
     * @param $className - class of model
     * @param $attribute - name of model attribute
     * @param $scenario - scenario of model
     *
     * @return bool
     */
    public function isAllowedForClass($className, $attribute, $scenario = Model::SCENARIO_DEFAULT)
    {
        $identity = $this->userIdentity;
        if ($identity) {
            $userId = $identity->getId();
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

    /**
     * @param $model - object of model
     * @param $attribute - name of model attribute
     *
     * @return bool
     */
    public function isAllowed($model, $attribute)
    {
        return $this->isAllowedForClass(get_class($model), $attribute, $model->scenario);
    }
}
