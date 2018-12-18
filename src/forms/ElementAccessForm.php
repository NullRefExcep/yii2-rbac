<?php

namespace nullref\rbac\forms;

use nullref\rbac\ar\ElementAccess;
use nullref\rbac\repositories\ElementAccessRepository;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class ElementAccessForm extends Model
{
    /** @var string */
    public $identificator;

    /** @var string */
    public $description;

    /** @var array */
    public $items;

    /** @var ElementAccessRepository */
    protected $elementAccessRepository;

    /**
     * ActionAccessForm constructor.
     *
     * @param ElementAccessRepository $elementAccessRepository
     */
    public function __construct(
        ElementAccessRepository $elementAccessRepository
    )
    {
        $this->elementAccessRepository = $elementAccessRepository;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identificator'], 'string', 'max' => 255],
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
            'identificator' => Yii::t('rbac', 'Identificator'),
            'description'   => Yii::t('rbac', 'Description'),
            'items'         => Yii::t('rbac', 'Items'),
        ];
    }

    /**
     * @return ActiveRecord
     */
    public function save()
    {
        return $this->elementAccessRepository->saveWithItems($this);
    }

    public function update(ElementAccess $elementAccess)
    {
        return $this->elementAccessRepository->updateWithItems($this, $elementAccess);
    }

    public function loadWithAR(ElementAccess $elementAccess)
    {
        $this->identificator = $elementAccess->identificator;
        $this->description = $elementAccess->description;
    }

}
