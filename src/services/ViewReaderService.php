<?php

namespace nullref\rbac\services;

use Yii;

class ViewReaderService
{
    public $aliases;

    function __construct()
    {
        $this->aliases = $this->prepareAliases();
    }

    public function prepareAliases()
    {
        $aliases = [];

        $modules = Yii::$app->modules;
        foreach ($modules as $moduleName => $module) {
            $module = Yii::$app->getModule($moduleName, false);
            if ($module) {
                if (isset($module->viewPathAliases)) {
                    foreach ($module->viewPathAliases as $alias) {
                        $aliases[] =
                            [
                                'alias'  => $alias,
                                'module' => $moduleName,
                            ];
                    }
                }
            }
        }

        return $aliases;
    }
}