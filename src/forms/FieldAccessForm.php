<?php

namespace nullref\rbac\forms;

use nullref\rbac\ar\FieldAccess;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class FieldAccessForm extends Model
{
    /** @var string */
    public $modelName;

    /** @var string */
    public $scenarioName;

    /** @var string */
    public $attributeName;

    /** @var string */
    public $description;

    /** @var array */
    public $items;

    /** @var FieldAccessRepositoryInterface */
    protected $fieldAccessRepository;

    /**
     * ActionAccessForm constructor.
     *
     * @param FieldAccessRepositoryInterface $fieldAccessRepository
     */
    public function __construct(
        FieldAccessRepositoryInterface $fieldAccessRepository
    )
    {
        $this->fieldAccessRepository = $fieldAccessRepository;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['modelName', 'scenarioName', 'attributeName'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['items'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'modelName'     => Yii::t('rbac', 'Model'),
            'scenarioName'  => Yii::t('rbac', 'Scenario'),
            'attributeName' => Yii::t('rbac', 'Attribute'),
            'description'   => Yii::t('rbac', 'Description'),
            'items'         => Yii::t('rbac', 'Items'),
        ];
    }

    /**
     * @return ActiveRecord
     */
    public function save()
    {
        return $this->fieldAccessRepository->saveWithItems($this);
    }

    public function update(FieldAccess $fieldAccess)
    {
        return $this->fieldAccessRepository->updateWithItems($this, $fieldAccess);
    }

    public function loadWithAR(FieldAccess $fieldAccess)
    {
        $this->modelName = $fieldAccess->model_name;
        $this->scenarioName = $fieldAccess->scenario_name;
        $this->attributeName = $fieldAccess->attribute_name;
        $this->description = $fieldAccess->description;
    }
}
