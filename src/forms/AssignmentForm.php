<?php

namespace nullref\rbac\forms;

use nullref\rbac\components\DbManager;
use nullref\rbac\components\ManagerInterface;
use nullref\rbac\repositories\AuthAssignmentRepository;
use nullref\rbac\validators\ItemsValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class AssignmentForm extends Model
{
    /**@var array */
    public $items = [];

    /** @var integer */
    public $userId;

    /** @var DbManager */
    protected $manager;

    /** @var AuthAssignmentRepository */
    protected $repository;

    /**
     * AssignmentForm constructor.
     *
     * @param DbManager $manager
     * @param AuthAssignmentRepository $repository
     */
    public function __construct(
        DbManager $manager,
        AuthAssignmentRepository $repository
    )
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->items = array_keys($this->manager->getItemsByUser($this->userId));

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
            ['user_id', 'required'],
            ['items', ItemsValidator::class],
            ['user_id', 'integer'],
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