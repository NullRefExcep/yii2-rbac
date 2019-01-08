<?php

namespace nullref\rbac\services;

use nullref\rbac\components\DBManager;
use nullref\rbac\Module;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use Yii;
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

    public function isAllowed($model, $attribute)
    {
        $identity = $this->userIdentity;
        if ($identity) {
            $userId = $identity->getId();
            $fieldItems = $this->fieldAccessRepository->findItems(get_class($model), $model->scenario, $attribute);
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