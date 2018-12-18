<?php

namespace nullref\rbac\widgets;

use Exception;
use nullref\rbac\Module;
use nullref\rbac\services\AssignmentService;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\di\NotInstantiableException;

class ElementConfig extends Widget
{
    /** @var string */
    private $elementEditorRole;

    /** @var object */
    private $userComponent;

    /** @var AssignmentService */
    private $assignmentService;

    /**
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function init()
    {
        parent::init();

        /** @var Module $module */
        $module = Yii::$app->getModule('rbac');
        $this->elementEditorRole = $module->elementEditorRole;
        $moduleUserComponent = $module->userComponent;
        try {
            $this->userComponent = Yii::$app->{$moduleUserComponent};
        } catch (Exception $e) {
            try {
                $this->userComponent = Yii::$app->getModule($moduleUserComponent);
            } catch (Exception $e) {
                throw new InvalidConfigException('Bad userComponent provided');
            }
        }

        $this->assignmentService = Yii::$container->get(AssignmentService::class);
    }

    /** @inheritdoc */
    public function run()
    {
        $userId = $this->userComponent->identity->getId();
        $userItems = $this->assignmentService->getUserAssignments($userId);

        if (in_array($this->elementEditorRole, $userItems)) {
            return $this->render('element-config');
        }

        return '';
    }
}