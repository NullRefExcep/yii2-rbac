<?php

namespace nullref\rbac\ar;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%element_access}}".
 *
 * @property int $id
 * @property string $identifier
 * @property string $description
 *
 * @property AuthItem[] $authItems
 */
class ElementAccess extends ActiveRecord
{
    public $items;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%element_access}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['identifier'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('rbac', 'ID'),
            'identifier' => Yii::t('rbac', 'Identifier'),
            'description'   => Yii::t('rbac', 'Description'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthItems()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'auth_item_name'])
            ->viaTable(ElementAccessItem::tableName(), ['element_access_id' => 'id']);
    }
}
