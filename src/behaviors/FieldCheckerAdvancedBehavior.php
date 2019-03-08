<?php

namespace nullref\rbac\behaviors;

use nullref\rbac\components\DBManager;
use nullref\rbac\enum\PermissionsMap;
use nullref\rbac\Module;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use Yii;
use yii\base\Behavior;
use yii\base\Model;
use yii\db\BaseActiveRecord;
use yii\web\User;

class FieldCheckerAdvancedBehavior extends Behavior
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
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'checkFieldsForCreate',
            BaseActiveRecord::EVENT_AFTER_FIND    => 'checkFieldsForView',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'checkFieldsForUpdate',
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'checkFieldsForDelete',
        ];
    }

    public function checkFieldForCreate()
    {

    }

    public function checkFieldsForView()
    {
        $attributesMap = [];
        /** @var Model $currentModel */
        $currentModel = $this->owner;
        $identity = $this->userIdentity;
        if ($identity) {
            $userId = $identity->getId();
            /** @var array $attributesItems */
            $attributesItems = $this->fieldAccessRepository
                ->findItemsForScenario(get_class($currentModel), $currentModel->scenario);
            if ($attributesItems) {
                foreach ($attributesItems as $attributeName => $attributeItems) {
                    foreach ($attributeItems as $item) {
                        if (!in_array($item->attribute_name, $attributesMap)) {
                            $attributesMap[$item->attribute_name] = true;
                        }
                        if ($this->manager->checkAccess($userId, $item)) {
                            $attributesMap[$item->attribute_name] = $attributesMap[$item->attribute_name] &&
                                $item->permission_map[PermissionsMap::PERMISSION_VIEW];
                            break;
                        }
                    }
                }
            }
        }

        foreach ($attributesMap as $attributeName => $attribute) {
            if (!$attribute) {
                unset($currentModel->$attributeName);
            }
        }
    }

    public function checkFieldsForUpdate()
    {

    }

    public function checkFieldsForDelete()
    {

    }
}
