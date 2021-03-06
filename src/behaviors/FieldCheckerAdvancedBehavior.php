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

    /** @var Model */
    private $currentModel;

    /** @var array */
    private $attributeItems = [];

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

        /** @var Model $currentModel */
        $currentModel = $this->owner;
        $this->currentModel = $currentModel;
        $this->attributeItems = $this->fieldAccessRepository
            ->findItemsForScenarioWithPermissions(get_class($currentModel), $currentModel->scenario);

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

        /** @var array $attributesItems */
        $attributesItems = $this->attributeItems;
        $identity = $this->userIdentity;

        if ($attributesItems) {
            foreach ($attributesItems as $attributeName => $attributeSetting) {
                $permissions = json_decode($attributeSetting['permissions']);
                foreach ($attributeSetting['items'] as $item) {
                    if (!in_array($attributeName, $attributesMap)) {
                        $attributesMap[$attributeName] = false;
                    }
                    if ($identity) {
                        $userId = $identity->getId();
                        if ($this->manager->checkAccess($userId, $item)) {
                            $attributesMap[$attributeName] = $attributesMap[$attributeName] ||
                                in_array(PermissionsMap::PERMISSION_VIEW, $permissions);
                            break;
                        }
                    }
                }
            }
        }

        foreach ($attributesMap as $attributeName => $attribute) {
            if (!$attribute) {
                unset($this->currentModel->$attributeName);
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
