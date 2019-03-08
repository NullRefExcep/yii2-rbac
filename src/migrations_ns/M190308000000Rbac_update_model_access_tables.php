<?php

namespace nullref\rbac\migrations_ns;

use nullref\core\traits\MigrationTrait;
use yii\db\Migration;

class M190308000000Rbac_update_model_access_tables extends Migration
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
