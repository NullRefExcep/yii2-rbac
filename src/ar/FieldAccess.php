<?php

namespace nullref\rbac\ar;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%field_access}}".
 *
 * @property int $id
 * @property string $model
 * @property string $description
 * @property string $field
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
            [['model', 'field'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('rbac', 'ID'),
            'model'       => Yii::t('rbac', 'Model'),
            'description' => Yii::t('rbac', 'Description'),
            'field'       => Yii::t('rbac', 'Field'),
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
