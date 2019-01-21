<?php

namespace nullref\rbac\services;

use nullref\rbac\Module;
use nullref\rbac\repositories\interfaces\ElementAccessRepositoryInterface;
use Yii;
use yii\web\User;

class ElementCheckerService
{
    /** @var ElementAccessRepositoryInterface */
    private $elementAccessRepository;

    /** @var UserAccessService */
    private $userAccessService;

    /** @var User|null */
    private $userIdentity;

    public function __construct(
        ElementAccessRepositoryInterface $elementAccessRepository,
        UserAccessService $userAccessService
    )
    {
        $this->elementAccessRepository = $elementAccessRepository;
        $this->userAccessService = $userAccessService;

        /** @var Module $module */
        $module = Yii::$app->getModule('rbac');
        $this->userIdentity = $module->getUserIdentity();
    }

    public function isAllowed($identifier)
    {
        $identity = $this->userIdentity;
        if ($identity) {
            $elementItems = $this->elementAccessRepository->findItems($identifier);
            if (empty($elementItems)) {
                return true;
            }

            foreach ($elementItems as $eItem) {
                if ($this->userAccessService->hasAccess($eItem)) {
                    return true;
                }
            }
        }

        return false;
    }
}