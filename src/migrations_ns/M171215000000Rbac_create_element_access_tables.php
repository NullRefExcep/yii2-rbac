<?php

namespace nullref\rbac\migrations_ns;

use nullref\core\traits\MigrationTrait;
use yii\db\Migration;

class M171215000000Rbac_create_element_access_tables extends Migration
{
    use MigrationTrait;

    public function safeUp()
    {
        $this->createTable('{{%element_access}}', [
            'id'          => $this->primaryKey(),
            'type'        => $this->integer(),
            'identificator'  => $this->string(),
            'description' => $this->text(),
        ], $this->getTableOptions());

        $this->createTable('{{%element_access_item}}', [
            'element_access_id' => $this->integer(),
            'auth_item_name'    => $this->string(),
        ], $this->getTableOptions());

        $this->addPrimaryKey('element_access_item_pk', '{{%element_access_item}}', [
            'element_access_id',
            'auth_item_name',
        ]);
    }

    public function safeDown()
    {
        $this->dropPrimaryKey('element_access_item_pk', '{{%element_access_item}}');

        $this->dropTable('{{%element_access_item}}');
        $this->dropTable('{{%element_access}}');

        return true;
    }
}
