<?php

namespace nullref\rbac\services;

use Yii;

class FieldReaderService
{
    public $map;

    function __construct()
    {
        $this->map = $this->createMap();
    }

    public function getModels()
    {
        $items = [];
        foreach (array_keys($this->map) as $val) {
            $items[$val] = $val;
        }

        return $items;
    }

    public function getModelsJs()
    {
        $items = [];
        foreach ($this->getModels() as $key => $val) {
            $items[$key] = [
                'id'   => $val,
                'name' => $val,
            ];
        }

        return $items;
    }

    public function getScenarios($model)
    {
        $items = [];
        if ($model) {
            if (isset($this->map[$model])) {
                foreach (array_keys($this->map[$model]) as $val) {
                    $items[$val] = $val;
                }
            }
        }

        return $items;
    }

    public function getScenariosJs($model)
    {
        $items = [];
        if ($model) {
            foreach ($this->getScenarios($model) as $key => $val) {
                $items[$key] = [
                    'id'   => $val,
                    'name' => $val,
                ];
            }
        }

        return $items;
    }

    public function getAttributes($module, $controller)
    {
        $items = [];
        if ($module && $controller) {
            if (isset($this->map[$module][$controller])) {
                foreach ($this->map[$module][$controller] as $val) {
                    $items[$val] = $val;
                }
            }
        }

        return $items;
    }

    public function getAttributesJs($module, $controller)
    {
        $items = [];
        if ($module && $controller) {
            foreach ($this->getAttributes($module, $controller) as $key => $val) {
                $items[$key] = [
                    'id'   => $val,
                    'name' => $val,
                ];
            }
        }

        return $items;
    }

    private function createMap()
    {
        $aliases = $this->prepareAliases();

        $modelList = [];
        foreach ($aliases as $alias) {
            $dirPath = Yii::getAlias($alias['alias']);
            $realPath = realpath($dirPath);
            if ($realPath && $handle = opendir($realPath)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $modelList[] = [
                            'file'   => $file,
                            'path'   => $realPath,
                            'module' => $alias['module'],
                        ];
                    }
                }
                closedir($handle);
            }
        }
        asort($modelList);
        $fullList = [];
        foreach ($modelList as $modelKey => $model) {
            $filePath = $model['path'] . '/' . $model['file'];

            $currentFile = file_get_contents($filePath, "r");
            $namescape = '';
            $match = [];
            $pattern = '/[\s]*namespace[\s]+(.*)[\s]*;/';
            if (preg_match($pattern, $currentFile, $match)) {
                if (isset($match[1])) {
                    $namescape = $match[1];
                }
            }
            if (!$namescape) {
                continue;
            }
            include $filePath;
            $class = str_replace('.php', '', $model['file']);
            $className = $namescape . "\\" . $class;
            $instance = new $className();

            if (!method_exists($instance, 'scenarios')) {
                continue;
            }
            $scenarios = $instance->scenarios();

            foreach ($scenarios as $scenario => $attributes) {
                $fullList[$className][$scenario] = $attributes;
            }
        }

        return $fullList;
    }

    private function prepareAliases()
    {
        $aliases = [];

        $modules = Yii::$app->modules;
        foreach ($modules as $moduleName => $module) {
            $module = Yii::$app->getModule($moduleName, false);
            if ($module) {
                $aliases = array_merge($aliases, $this->readModule($moduleName, $module));
            }
        }

        return $aliases;
    }

    private function readModule($moduleName, $module)
    {
        $aliases = [];

        if (isset($module->modelAliases)) {
            foreach ($module->modelAliases as $alias) {
                $aliases[] =
                    [
                        'alias'  => $alias,
                        'module' => $moduleName,
                    ];
            }
        }
        if ($module->modules) {
            foreach ($module->modules as $subModuleName => $subModule) {
                $subModule = $module->getModule($subModuleName, false);
                if ($subModule) {
                    $aliases = array_merge($aliases, $this->readModule($subModuleName, $subModule));
                }
            }
        }

        return $aliases;
    }
}