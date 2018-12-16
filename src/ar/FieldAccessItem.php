<?php

namespace nullref\rbac\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%field_access_item}}".
 *
 * @property int $field_access_id
 * @property string $auth_item_name
 */
class FieldAccessItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%field_access_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['field_access_id', 'auth_item_name'], 'required'],
            [['field_access_id'], 'integer'],
            [['auth_item_name'], 'string', 'max' => 255],
            [
                ['field_access_id', 'auth_item_name'],
                'unique',
                'targetAttribute' => ['field_access_id', 'auth_item_name'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'field_access_id' => Yii::t('rbac', 'Field Access ID'),
            'auth_item_name'  => Yii::t('rbac', 'Auth Item Name'),
        ];
    }

    public static function getActionItems($fieldId)
    {
        return self::find()
            ->select(['auth_item_name'])
            ->where(['field_access_id' => $fieldId])
            ->column();
    }
}
