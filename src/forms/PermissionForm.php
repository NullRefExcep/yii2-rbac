<?php

namespace nullref\rbac\forms;

use nullref\rbac\components\DBManager;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\PermissionRepository;
use yii\rbac\Item;
use Yii;

class PermissionForm extends ItemForm
{
    /** @var string  */
    public $rule;

    /** @var array|string */
    public $data;

    /** @var AuthItemRepository */
    private $authItemRepository;

    /** @var PermissionRepository */
    private $permissionRepository;


    /**
     * RoleForm constructor.
     *
     * @param AuthItemRepository $authItemRepository
     * @param PermissionRepository $permissionRepository
     * @param DBManager $manager
     * @param array $config
     */
    public function __construct(
        AuthItemRepository $authItemRepository,
        PermissionRepository $permissionRepository,
        DBManager $manager,
        $config = []
    )
    {
        $this->authItemRepository = $authItemRepository;
        $this->permissionRepository = $permissionRepository;

        parent::__construct($manager, $config);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
           'rule' => Yii::t('rbac', 'Rule'),
           'data' => Yii::t('rbac', 'Data')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'description', 'children', 'rule', 'data'],
            'update' => ['name', 'description', 'children', 'rule', 'data'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['rule'], 'trim'],
            [
                'rule',
                function () {
                    $rule = $this->manager->getRule($this->rule);

                    if (!$rule) {
                        $this->addError('rule', Yii::t('rbac', 'Rule {0} does not exist', $this->rule));
                    }
                },
            ],
            [
                'data',
                function () {
                    try {
                        Json::decode($this->data);
                    } catch (InvalidParamException $e) {
                        $this->addError('data', Yii::t('rbac', 'Data must be type of JSON ({0})', $e->getMessage()));
                    }
                },
            ],
        ]);
    }

    /**
     * @return mixed
     */
    public function getUnassignedItems()
    {
        return $this->repository->getUnassignedItems($this->item, Item::TYPE_PERMISSION);
    }

    /**
     * @param string $name
     *
     * @return Item
     */
    protected function createItem($name)
    {
        return $this->manager->createPermission($name);
    }
}