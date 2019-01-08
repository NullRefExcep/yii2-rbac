<?php

namespace nullref\rbac\services;

use nullref\rbac\components\DBManager;
use nullref\rbac\Module;
use nullref\rbac\repositories\interfaces\ElementAccessRepositoryInterface;
use Yii;
use yii\web\User;

class ElementCheckerService
{
    /** @var DBManager */
    private $manager;

    /** @var ElementAccessRepositoryInterface */
    private $elementAccessRepository;

    /** @var User|null */
    private $userIdentity;

    public function __construct(
        DBManager $manager,
        ElementAccessRepositoryInterface $elementAccessRepository
    )
    {
        $this->manager = $manager;
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
            $elementItems = $this->elementAccessRepository->findItems($identifier);
            if (empty($elementItems)) {
                return true;
            }

            foreach ($elementItems as $eItem) {
                if ($this->manager->checkAccess($userId, $eItem)) {
                    return true;
                }
            }
        }

        return false;
    }
}