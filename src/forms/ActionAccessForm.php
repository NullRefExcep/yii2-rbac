<?php

namespace nullref\rbac\forms;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\repositories\ActionAccessRepository;
use Yii;
use yii\base\Model;

class ActionAccessForm extends Model
{
    /** @var string */
    public $module;

    /** @var string */
    public $controller;

    /** @var $action */
    public $action;

    /** @var array */
    public $items;

    /** @var ActionAccessRepository */
    protected $repository;

    /**
     * ActionAccessForm constructor.
     *
     * @param ActionAccessRepository $repository
     */
    public function __construct(
        ActionAccessRepository $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module', 'controller', 'action'], 'string', 'max' => 255],
            [['items'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'module'     => Yii::t('rbac', 'Module'),
            'controller' => Yii::t('rbac', 'Controller'),
            'action'     => Yii::t('rbac', 'Action'),
            'items'      => Yii::t('rbac', 'Items'),
        ];
    }

    /**
     * @return \nullref\rbac\repositories\ActiveRecord
     */
    public function save()
    {
        return $this->repository->saveWithItems($this);
    }

    public function update(ActionAccess $actionAccess)
    {
        return $this->repository->updateWithItems($this, $actionAccess);
    }

    public function loadWithAR(ActionAccess $actionAccess)
    {
        $this->module = $actionAccess->module;
        $this->controller = $actionAccess->controller;
        $this->action = $actionAccess->action;
    }

}
