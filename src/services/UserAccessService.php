<?php

namespace nullref\rbac\services;

use nullref\rbac\Module;
use Yii;

class UserAccessService
{
    /** @var AssignmentService */
    private $assignmentService;

    /** @var AuthTreeService */
    private $authTreeService;

    /** @var bool */
    private $isInitialized = false;

    /** @var array  */
    private $currentItems = [];

    /** @var array  */
    private $systemItems = [];

    public function __construct(
        AssignmentService $assignmentService,
        AuthTreeService $authTreeService
    )
    {
        $this->assignmentService = $assignmentService;
        $this->authTreeService = $authTreeService;

        /** @var Module $module */
        $module = Yii::$app->getModule('rbac');
        $this->userIdentity = $module->getUserIdentity();

        $this->initialize();
    }

    public function hasAccess($item)
    {
        foreach ($this->currentItems as $userItemName) {
            if ($item == $userItemName) {
                return true;
            }
            $childrenItemNames = $this->systemItems[$userItemName];
            if (in_array($item, $childrenItemNames)) {
                return true;
            }
        }

        return false;
    }

    private function initialize()
    {
        if (!$this->isInitialized) {
            $identity = $this->userIdentity;
            if ($identity) {
                $userId = $identity->getId();
                $this->currentItems = $this->assignmentService->getUserAssignments($userId);
            }

            $this->systemItems = $this->authTreeService->getArrayAuthList(
                $this->authTreeService->getAuthTree()
            );
        }
        $this->initialized = true;
    }

}