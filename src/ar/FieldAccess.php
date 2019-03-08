<?php

namespace nullref\rbac\ar;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%field_access}}".
 *
 * @property int $id
 * @property string $model_name
 * @property string $scenario_name
 * @property string $attribute_name
 * @property string $description
 * @property string $permissions_map
 *
 * @property AuthItem[] $authItems
 */
class FieldAccess extends ActiveRecord
{
    public $items;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%field_access}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['permissions_map'], 'safe'],
            [['model_name', 'scenario_name', 'attribute_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => Yii::t('rbac', 'ID'),
            'model_name'      => Yii::t('rbac', 'Model'),
            'scenario_name'   => Yii::t('rbac', 'Scenario'),
            'attribute_name'  => Yii::t('rbac', 'Attribute'),
            'description'     => Yii::t('rbac', 'Description'),
            'permissions_map' => Yii::t('rbac', 'Permissions map'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthItems()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'auth_item_name'])
            ->viaTable(FieldAccessItem::tableName(), ['field_access_id' => 'id']);
    }
}
