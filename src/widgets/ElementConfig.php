<?php

namespace nullref\rbac\widgets;

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
    private $userIdentity;

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
        $this->userIdentity = $module->getUserIdentity();
        $this->elementEditorRole = $module->elementEditorRole;

        $this->assignmentService = Yii::$container->get(AssignmentService::class);
    }

    /** @inheritdoc */
    public function run()
    {
        $identity = $this->userIdentity;
        if ($identity) {
            $userId = $identity->getId();
            $userItems = $this->assignmentService->getUserAssignments($userId);

            if (in_array($this->elementEditorRole, $userItems)) {
                return $this->render('element-config');
            }
        }

        return '';
    }
}