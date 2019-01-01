<?php

namespace nullref\rbac\forms;

use nullref\rbac\repositories\interfaces\ActionAccessRepositoryInterface;
use Yii;
use yii\base\Model;

class ActionAccessAssignForm extends Model
{
    /** @var integer */
    public $actionId;

    /** @var array */
    public $items;

    /** @var ActionAccessRepositoryInterface */
    private $repository;

    /**
     * ActionAccessForm constructor.
     *
     * @param ActionAccessRepositoryInterface $repository
     */
    public function __construct(
        ActionAccessRepositoryInterface $repository
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
            [['actionId'], 'integer'],
            [['items'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'actionId' => Yii::t('rbac', 'Action ID'),
            'items'    => Yii::t('rbac', 'Items'),
        ];
    }

    public function assignItems()
    {
        if (!is_array($this->items)) {
            $this->items = [];
        }

        $this->repository->assignItems($this->actionId, $this->items);

        return true;
    }
}
