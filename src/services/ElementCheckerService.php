<?php

namespace nullref\rbac\services;

use nullref\rbac\repositories\ElementAccessRepository;
use Yii;
use yii\base\InvalidConfigException;

class ElementCheckerService
{
    /** @var object  */
    private $userComponent;

    /** @var AssignmentService */
    private $assignmentService;

    /** @var ElementAccessService */
    private $elementAccessService;

    /** @var ElementAccessRepository */
    private $elementAccessRepository;

    public function __construct(
        AssignmentService $assignmentService,
        ElementAccessService $elementAccessService,
        ElementAccessRepository $elementAccessRepository
    )
    {
        $this->assignmentService = $assignmentService;
        $this->elementAccessService = $elementAccessService;
        $this->elementAccessRepository = $elementAccessRepository;

        $moduleUserComponent = Yii::$app->getModule('rbac')->userComponent;
        try {
            $this->userComponent = Yii::$app->{$moduleUserComponent};
        } catch (\Exception $e) {
            try {
                $this->userComponent = Yii::$app->getModule($moduleUserComponent);
            } catch (\Exception $e) {
                throw new InvalidConfigException('Bad userComponent provided');
            }
        }
    }

    public function isAllowed($identificator)
    {
        $userId = $this->userComponent->identity->getId();
        $userItems = $this->assignmentService->getUserAssignments($userId);
        $elementItems = $this->elementAccessRepository->findItems($identificator);
        if (empty($elementItems)) {
            return true;
        }

        $intersect = array_intersect($userItems, $elementItems);

        return (count($intersect) != 0) ? true : false;
    }
}