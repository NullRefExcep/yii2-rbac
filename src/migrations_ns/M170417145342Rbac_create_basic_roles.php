<?php

namespace nullref\rbac\migrations_ns;

use nullref\rbac\ar\AuthItem;
use nullref\rbac\ar\AuthItemChild;
use nullref\rbac\ar\AuthRule;
use nullref\rbac\components\DbManager;
use yii\db\Migration;
use yii\rbac\Item;

class M170417145342Rbac_create_basic_roles extends Migration
{
    public function up()
    {
        AuthItem::deleteAll();

        /** @var DbManager $authManager */
        $authManager = Yii::$app->getAuthManager();
        $time = time();
        $this->batchInsert($authManager->itemTable, [
            'name',
            'type',
            'description',
            'rule_name',
            'data',
            'created_at',
            'updated_at',
        ], [
            //Administrator
            ['administrator', Item::TYPE_ROLE, 'administrator', null, null, $time, $time],
            //Dash board
            ['dashboard', Item::TYPE_ROLE, 'dashboard', null, null, $time, $time],
        ]);

        $this->batchInsert($authManager->itemChildTable, ['parent', 'child'], [
            //Administrator has
            ['administrator', 'dashboard'],

        ]);
    }

    public function down()
    {
        AuthRule::deleteAll();
        AuthItemChild::deleteAll();
        AuthItem::deleteAll();

        return true;
    }
}