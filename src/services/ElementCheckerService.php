<?php

namespace nullref\rbac\services;

use nullref\rbac\Module;
use nullref\rbac\repositories\ElementAccessRepository;
use Yii;
use yii\web\User;

class ElementCheckerService
{
    /** @var object */
    private $userComponent;

    /** @var AssignmentService */
    private $assignmentService;

    /** @var ElementAccessService */
    private $elementAccessService;

    /** @var ElementAccessRepository */
    private $elementAccessRepository;

    /** @var User|null */
    private $userIdentity;

    public function __construct(
        AssignmentService $assignmentService,
        ElementAccessService $elementAccessService,
        ElementAccessRepository $elementAccessRepository
    )
    {
        $this->assignmentService = $assignmentService;
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
            $userItems = $this->assignmentService->getUserAssignments($userId);
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