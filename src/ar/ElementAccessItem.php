<?php

namespace nullref\rbac\ar;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%element_access_item}}".
 *
 * @property int $element_access_id
 * @property string $auth_item_name
 */
class ElementAccessItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%element_access_item}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['element_access_id', 'auth_item_name'], 'required'],
            [['element_access_id'], 'integer'],
            [['auth_item_name'], 'string', 'max' => 255],
            [
                ['element_access_id', 'auth_item_name'],
                'unique',
                'targetAttribute' => ['element_access_id', 'auth_item_name'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'element_access_id' => Yii::t('rbac', 'Element Access ID'),
            'auth_item_name'  => Yii::t('rbac', 'Auth Item Name'),
        ];
    }

    public static function getActionItems($elementId)
    {
        return self::find()
            ->select(['auth_item_name'])
            ->where(['element_access_id' => $elementId])
            ->column();
    }
}
