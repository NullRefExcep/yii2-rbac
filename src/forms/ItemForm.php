<?php

namespace nullref\rbac\forms;

use nullref\rbac\components\DbManager;
use nullref\rbac\validators\ItemsValidator;
use Yii;
use yii\base\Model;
use yii\rbac\Item;

abstract class ItemForm extends Model
{
    /** @var string */
    public $name;

    /** @var integer */
    public $type;

    /** @var srting */
    public $description;

    /** @var array */
    public $children = [];

    /** @var bool */
    public $dataCannotBeDecoded = false;

    /**@var Item */
    public $item;

    /** @var DbManager */
    protected $manager;

    public function __construct(
        DbManager $manager,
        $config = []
    )
    {
        $this->manager = $manager;

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        if ($this->item instanceof Item) {
            $itemName = $this->item->name;
            $this->name = $itemName;
            $this->description = $this->item->description;
            $this->children = array_keys($this->manager->getChildren($itemName));

            try {
                if (is_object($this->item->data)) {
                    $this->dataCannotBeDecoded = true;
                } else if ($this->item->data !== null) {
                    $this->data = Json::encode($this->item->data);
                }
            } catch (InvalidParamException $e) {
                $this->dataCannotBeDecoded = true;
            }

            if ($this->item->ruleName !== null) {
                $this->rule = get_class($this->manager->getRule($this->item->ruleName));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'        => Yii::t('rbac', 'Name'),
            'description' => Yii::t('rbac', 'Description'),
            'children'    => Yii::t('rbac', 'Children'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'description', 'children'],
            'update' => ['name', 'description', 'children'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['name', 'description', 'rule'], 'trim'],
            [
                'name',
                function () {
                    if ($this->manager->getItem($this->name) !== null) {
                        $this->addError('name', Yii::t('rbac', 'Auth item with such name already exists'));
                    }
                },
                'when' => function () {
                    return ($this->scenario == 'create' || $this->item->name != $this->name);
                },
            ],
            ['children', ItemsValidator::class],
        ];
    }

    /**
     * Saves item.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->validate() == false) {
            return false;
        }

        if ($isNewItem = ($this->item === null)) {
            $this->item = $this->createItem($this->name);
        } else {
            $oldName = $this->item->name;
        }

        $this->item->name = $this->name;
        $this->item->description = $this->description;
        if (isset($this->data)) {
            $this->item->data = $this->data == null ? null : Json::decode($this->data);
        }
        if (isset($this->rule)) {
            $this->item->ruleName = empty($this->rule) ? null : $this->rule;
        }

        if ($isNewItem) {
            $this->manager->add($this->item);
        } else {
            $this->manager->update($oldName, $this->item);
        }

        $this->updateChildren();

        $this->manager->invalidateCache();

        return true;
    }

    /**
     * Updated items children.
     */
    protected function updateChildren()
    {
        $children = $this->manager->getChildren($this->item->name);
        $childrenNames = array_keys($children);

        if (is_array($this->children)) {
            // remove children that
            foreach (array_diff($childrenNames, $this->children) as $item) {
                $this->manager->removeChild($this->item, $children[$item]);
            }
            // add new children
            foreach (array_diff($this->children, $childrenNames) as $item) {
                $this->manager->addChild($this->item, $this->manager->getItem($item));
            }
        } else {
            $this->manager->removeChildren($this->item);
        }
    }

    abstract public function getUnassignedItems();

    /**
     * @param  string $name
     *
     * @return Item
     */
    abstract protected function createItem($name);
}