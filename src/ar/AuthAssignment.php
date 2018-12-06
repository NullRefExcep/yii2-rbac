<?php

namespace nullref\rbac\ar;

use nullref\rbac\Module;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%auth_assignment}}".
 *
 * @property string $item_name
 * @property string $user_id
 * @property integer $created_at
 *
 * @property AuthItem $itemName
 */
class AuthAssignment extends ActiveRecord
{
    /** @var Module */
    private $module;

    public function init()
    {
        $module = Yii::$app->getModule('rbac');
        if (!($module instanceof Module)) {
            throw new \Exception('RBAC not installed');
        }
        $this->module = $module;

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_assignment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at'], 'integer'],
            [['item_name', 'user_id'], 'string', 'max' => 64],
            [
                ['item_name'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => AuthItem::class,
                'targetAttribute' => ['item_name' => 'name'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name'  => Yii::t('rbac', 'Item Name'),
            'user_id'    => Yii::t('rbac', 'User ID'),
            'created_at' => Yii::t('rbac', 'Created At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(AuthItem::class, ['name' => 'item_name']);
    }

    //TODO
    public function getUser()
    {
        return $this->hasOne($this->module->userActiveRecordClass, ['id' => 'user_id']);
    }

}
