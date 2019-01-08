<?php

namespace nullref\rbac\behaviors;

use nullref\rbac\components\DBManager;
use nullref\rbac\Module;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use Yii;
use yii\base\Behavior;
use yii\base\Model;
use yii\web\User;

class FieldCheckerBehavior extends Behavior
{
    /** @var DBManager */
    private $manager;

    /** @var FieldAccessRepositoryInterface */
    private $fieldAccessRepository;

    /** @var User|null */
    private $userIdentity;

    public function __construct(
        array $config = [],
        DBManager $manager,
        FieldAccessRepositoryInterface $fieldAccessRepository
    )
    {
        $this->manager = $manager;
        $this->fieldAccessRepository = $fieldAccessRepository;

        /** @var Module $module */
        $module = Yii::$app->getModule('rbac');
        $this->userIdentity = $module->getUserIdentity();

        parent::__construct($config);
    }

    /**
     * Events list
     * @return array
     */
    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => 'checkFields',
        ];
    }

    public function checkFields()
    {
        $identity = $this->userIdentity;
        if ($identity) {
            $userId = $identity->getId();
            /** @var Model $currentModel */
            $currentModel = $this->owner;
            /** @var array $attributesItems */
            $attributesItems = $this->fieldAccessRepository->findItemsForScenario(get_class($currentModel), $currentModel->scenario);
            if ($attributesItems) {
                foreach ($attributesItems as $attributeName => $attributeItems) {
                    if ($currentModel->isAttributeChanged($attributeName)) {
                        $allowed = false;
                        foreach ($attributeItems as $item) {
                            if ($this->manager->checkAccess($userId, $item)) {
                                $allowed = true;
                                break;
                            }
                        }
                        if (!$allowed) {
                            $currentModel->addError($attributeName, Yii::t('rbac', 'You are not allowed to work with attribute "{attribute}"'));
                        }
                    }
                }
            }
        }
    }
}
