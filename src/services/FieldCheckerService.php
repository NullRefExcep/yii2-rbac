<?php

namespace nullref\rbac\services;

use nullref\rbac\components\DBManager;
use nullref\rbac\Module;
use nullref\rbac\repositories\interfaces\AuthAssignmentRepositoryInterface;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use Yii;
use yii\web\User;

class FieldCheckerService
{
    /** @var DBManager */
    private $manager;

    /** @var AuthAssignmentRepositoryInterface */
    private $authAssignmentRepository;

    /** @var FieldAccessService */
    private $fieldAccessService;

    /** @var FieldAccessRepositoryInterface */
    private $fieldAccessRepository;

    /** @var User|null */
    private $userIdentity;

    public function __construct(
        DBManager $manager,
        AuthAssignmentRepositoryInterface $authAssignmentRepository,
        FieldAccessService $fieldAccessService,
        FieldAccessRepositoryInterface $fieldAccessRepository
    )
    {
        $this->manager = $manager;
        $this->authAssignmentRepository = $authAssignmentRepository;
        $this->fieldAccessService = $fieldAccessService;
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
//            $userItems = array_keys($this->authAssignmentRepository->getUserAssignments($userId));
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