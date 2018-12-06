<?php

namespace nullref\rbac\ar;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%action_access}}".
 *
 * @property integer $id
 * @property string $module
 * @property string $controller
 * @property string $action
 *
 * @property AuthItem[] $authItems
 */
class ActionAccess extends ActiveRecord
{
    public $items;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%action_access}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module', 'controller', 'action'], 'string', 'max' => 255],
            [['items'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rbac', 'ID'),
            'module' => Yii::t('rbac', 'Module'),
            'controller' => Yii::t('rbac', 'Controller'),
            'action' => Yii::t('rbac', 'Action'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthItems()
    {
        return $this->hasMany(AuthItem::class, ['name' => 'auth_item_name'])
            ->viaTable(ActionAccessItem::tableName(), ['action_access_id' => 'id']);
    }
}
