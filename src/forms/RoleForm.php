<?php

namespace nullref\rbac\forms;

use nullref\rbac\components\DBManager;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\RoleRepository;
use yii\rbac\Item;

class RoleForm extends ItemForm
{
    /** @var AuthItemRepository */
    private $authItemRepository;

    /** @var RoleRepository */
    private $roleRepository;

    /**
     * RoleForm constructor.
     *
     * @param AuthItemRepository $authItemRepository
     * @param RoleRepository $roleRepository
     * @param DBManager $manager
     * @param array $config
     */
    public function __construct(
        AuthItemRepository $authItemRepository,
        RoleRepository $roleRepository,
        DBManager $manager,
        $config = []
    )
    {
        $this->authItemRepository = $authItemRepository;
        $this->roleRepository = $roleRepository;

        parent::__construct($manager, $config);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return parent::attributeLabels();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return parent::rules();
    }

    /**
     * @return array
     */
    public function getUnassignedItems()
    {
        return $this->authItemRepository->getUnassignedItems($this->item);
    }

    /**
     * @param string $name
     *
     * @return Item
     */
    protected function createItem($name)
    {
        return $this->manager->createRole($name);
    }
}