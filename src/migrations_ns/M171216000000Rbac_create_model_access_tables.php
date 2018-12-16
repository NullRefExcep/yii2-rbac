<?php

namespace nullref\rbac\migrations_ns;

use nullref\core\traits\MigrationTrait;
use yii\db\Migration;

class M171216000000Rbac_create_model_access_tables extends Migration
{
    use MigrationTrait;

    public function safeUp()
    {
        $this->createTable('{{%field_access}}', [
            'id' => $this->primaryKey(),
            'model' => $this->string(),
            'description' => $this->text(),
            'field' => $this->string(),
        ], $this->getTableOptions());

        $this->createTable('{{%field_access_item}}', [
            'field_access_id' => $this->integer(),
            'auth_item_name' => $this->string(),
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
