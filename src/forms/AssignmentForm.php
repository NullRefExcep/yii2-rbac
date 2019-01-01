<?php

namespace nullref\rbac\forms;

use nullref\rbac\components\DBManager;
use nullref\rbac\repositories\interfaces\AuthAssignmentRepositoryInterface;
use nullref\rbac\validators\ItemsValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class AssignmentForm extends Model
{
    /**@var array */
    public $items = [];

    /** @var DBManager */
    private $manager;

    /** @var AuthAssignmentRepositoryInterface */
    private $repository;

    /** @var integer */
    public $userId;

    /**
     * AssignmentForm constructor.
     *
     * @param DBManager $manager
     * @param AuthAssignmentRepositoryInterface $repository
     * @param int $userId
     */
    public function __construct(
        DBManager $manager,
        AuthAssignmentRepositoryInterface $repository,
        int $userId
    )
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->userId = $userId;
        $this->items = array_keys($this->repository->getUserAssignments($this->userId));

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'items' => Yii::t('rbac', 'Items'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['userId', 'required'],
            ['items', ItemsValidator::class],
            ['userId', 'integer'],
        ];
    }

    /**
     * Updates auth assignments for user.
     * @return boolean
     */
    public function updateAssignments()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->repository->updateAssignments($this->userId, $this->items);

        return true;
    }

    /**
     * Returns all available auth items to be attached to user.
     * @return array
     */
    public function getAvailableItems()
    {
        return ArrayHelper::map(
            $this->manager->getItems(),
            'name',
            function ($item) {
                return empty($item->description)
                    ? $item->name
                    : $item->name . ' (' . $item->description . ')';
            }
        );
    }
}