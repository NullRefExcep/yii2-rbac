<?php

namespace nullref\rbac\services;

use nullref\rbac\Module;
use nullref\rbac\repositories\interfaces\AuthAssignmentRepositoryInterface;
use nullref\rbac\repositories\interfaces\ElementAccessRepositoryInterface;
use Yii;
use yii\web\User;

class ElementCheckerService
{
    /** @var object */
    private $userComponent;

    /** @var AuthAssignmentRepositoryInterface */
    private $authAssignmentRepository;

    /** @var ElementAccessService */
    private $elementAccessService;

    /** @var ElementAccessRepositoryInterface */
    private $elementAccessRepository;

    /** @var User|null */
    private $userIdentity;

    public function __construct(
        AuthAssignmentRepositoryInterface $authAssignmentRepository,
        ElementAccessService $elementAccessService,
        ElementAccessRepositoryInterface $elementAccessRepository
    )
    {
        $this->authAssignmentRepository = $authAssignmentRepository;
        $this->elementAccessService = $elementAccessService;
        $this->elementAccessRepository = $elementAccessRepository;

        /** @var Module $module */
        $module = Yii::$app->getModule('rbac');
        $this->userIdentity = $module->getUserIdentity();
    }

    public function isAllowed($identifier)
    {
        $identity = $this->userIdentity;
        if ($identity) {
            $userId = $identity->getId();
            $userItems = array_keys($this->authAssignmentRepository->getUserAssignments($userId));
            $elementItems = $this->elementAccessRepository->findItems($identifier);
            if (empty($elementItems)) {
                return true;
            }

            $intersect = array_intersect($userItems, $elementItems);

            return (count($intersect) != 0) ? true : false;
        }

        return false;
    }
}