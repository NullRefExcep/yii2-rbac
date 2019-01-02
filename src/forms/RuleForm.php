<?php

namespace nullref\rbac\forms;

use nullref\rbac\components\DBManager;
use nullref\rbac\repositories\AuthItemRepository;
use yii\base\Model;
use yii\rbac\Rule;
use Yii;

class RuleForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /** @var string */
    public $name;

    /** @var string */
    public $class;

    /** @var srting */
    private $oldName;

    /** @var DBManager */
    private $manager;

    /**
     * RoleForm constructor.
     *
     * @param AuthItemRepository $authItemRepository
     * @param DBManager $manager
     * @param array $config
     */
    public function __construct(
        DBManager $manager,
        $config = []
    )
    {
        $this->manager = $manager;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['name', 'class'],
            self::SCENARIO_UPDATE => ['name', 'class'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return parent::attributeLabels();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'class'], 'trim'],
            [['name', 'class'], 'required'],
            ['name', 'match', 'pattern' => '/^[\w][\w-.:]+[\w]$/'],
            [
                'name',
                function () {
                    if ($this->name == $this->oldName) {
                        return;
                    }
                    $rule = $this->manager->getRule($this->name);

                    if ($rule instanceof \yii\rbac\Rule) {
                        $this->addError('name', Yii::t('rbac', 'Name is already in use'));
                    }
                },
            ],
            [
                'class',
                function () {
                    if (!class_exists($this->class)) {
                        $this->addError('class', Yii::t('rbac', 'Class "{0}" does not exist', $this->class));
                    } else {
                        try {
                            $class = Rule::class;
                            $rule = Yii::createObject($this->class);

                            if (!($rule instanceof $class)) {
                                $this->addError('class', Yii::t('rbac', 'Rule class must extend'). ' yii\rbac\Rule');
                            }
                        } catch (InvalidConfigException $e) {
                            $this->addError('class', Yii::t('rbac', 'Rule class can not be instantiated'));
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Creates new auth rule.
     *
     * @return bool
     * @throws InvalidConfigException
     */
    public function create()
    {
        if ($this->scenario != self::SCENARIO_CREATE) {
            return false;
        }

        if (!$this->validate()) {
            return false;
        }

        $rule = yii::createObject([
            'class' => $this->class,
            'name'  => $this->name,
        ]);

        $this->manager->add($rule);
        $this->manager->invalidateCache();

        return true;
    }

    /**
     * Updates existing auth rule.
     *
     * @return bool
     * @throws InvalidConfigException
     */
    public function update()
    {
        if ($this->scenario != self::SCENARIO_UPDATE) {
            return false;
        }

        if (!$this->validate()) {
            return false;
        }

        $rule = yii::createObject([
            'class' => $this->class,
            'name'  => $this->name,
        ]);

        $this->manager->update($this->oldName, $rule);
        $this->manager->invalidateCache();

        return true;
    }

    public function setOldName($oldName)
    {
        $this->oldName = $oldName;
    }
}