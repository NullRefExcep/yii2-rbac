<?php

use nullref\core\traits\MigrationTrait;
use yii\db\Migration;

class m190308_000000_rbac_update_model_access_tables extends Migration
{
    use MigrationTrait;

    public function safeUp()
    {
        $this->addColumn('{{%field_access}}', 'permissions_map', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('{{%field_access}}', 'permissions_map');

        return true;
    }
}
