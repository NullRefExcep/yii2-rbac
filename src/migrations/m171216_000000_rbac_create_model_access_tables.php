<?php

use nullref\core\traits\MigrationTrait;
use yii\db\Migration;

class m171216_000000_rbac_create_model_access_tables extends Migration
{
    use MigrationTrait;

    public function safeUp()
    {
        $this->createTable('{{%field_access}}', [
            'id'             => $this->primaryKey(),
            'model_name'     => $this->string(),
            'scenario_name'  => $this->string(),
            'attribute_name' => $this->string(),
            'description'    => $this->text(),
        ], $this->getTableOptions());

        $this->createTable('{{%field_access_item}}', [
            'field_access_id' => $this->integer(),
            'auth_item_name'  => $this->string(),
        ], $this->getTableOptions());

        $this->addPrimaryKey('field_access_item_pk', '{{%field_access_item}}', ['field_access_id', 'auth_item_name']);
    }

    public function safeDown()
    {
        $this->dropPrimaryKey('field_access_item_pk', '{{%field_access_item}}');

        $this->dropTable('{{%field_access_item}}');
        $this->dropTable('{{%field_access}}');

        return true;
    }
}
