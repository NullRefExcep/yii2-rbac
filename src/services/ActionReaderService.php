<?php

namespace nullref\rbac\services;

use Yii;

class ActionReaderService
{
    public $map;

    function __construct()
    {
        $this->map = $this->createMap();
    }

    public function getModules()
    {
        $items = [];
        foreach (array_keys($this->map) as $val) {
            $items[$val] = $val;
        }

        return $items;
    }

    public function getModulesJs()
    {
        $items = [];
        foreach ($this->getModules() as $key => $val) {
            $items[$key] = [
                'id'   => $val,
                'name' => $val,
            ];
        }

        return $items;
    }

    public function getControllers($module)
    {
        $items = [];
        if ($module) {
            if (isset($this->map[$module])) {
                foreach (array_keys($this->map[$module]) as $val) {
                    $items[$val] = $val;
                }
            }
        }

        return $items;
    }

    public function getControllersJs($module)
    {
        $items = [];
        if ($module) {
            foreach ($this->getControllers($module) as $key => $val) {
                $items[$key] = [
                    'id'   => $val,
                    'name' => $val,
                ];
            }
        }

        return $items;
    }

    public function getActions($module, $controller)
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

    public function getActionsJs($module, $controller)
    {
        $items = [];
        if ($module && $controller) {
            foreach ($this->getActions($module, $controller) as $key => $val) {
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

        $controllerList = [];
        foreach ($aliases as $alias) {
            $dirPath = Yii::getAlias($alias['alias']);
            $realPath = realpath($dirPath);
            $controllerList = array_merge($controllerList, $this->readDir($realPath, $alias['module']));
        }
        asort($controllerList);
        $fullList = [];
        foreach ($controllerList as $controller) {
            $handle = fopen($controller['path'] . '/' . $controller['file'], "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/public function action(.*?)\(/', $line, $display)) {
                        if (strlen($display[1]) > 2) {
                            $controllerName = substr($controller['file'], 0, -14);
                            $controllerPieces = preg_split(
                                '/(?=[A-Z])/',
                                $controllerName,
                                -1,
                                PREG_SPLIT_NO_EMPTY
                            );
                            $controllerSeparatedName = implode('-', $controllerPieces);
                            $controllerName = strtolower($controllerSeparatedName);
                            $controllerName = $controller['dirPath'] . '-' . $controllerName;
                            $controllerName = trim($controllerName, '-');

                            $actionPecies = preg_split(
                                '/(?=[A-Z])/',
                                $display[1],
                                -1,
                                PREG_SPLIT_NO_EMPTY
                            );
                            $actionSeparatedName = implode('-', $actionPecies);
                            $actionName = strtolower($actionSeparatedName);

                            $fullList[$controller['module']][$controllerName][] = strtolower($actionName);
                        }
                    }
                }
            }
            fclose($handle);
        }

        return $fullList;
    }

    private function readDir($realPath, $module, $dirName = '')
    {
        $controllerList = [];

        if ($realPath && $handle = opendir($realPath)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $newRealPath = $realPath . '/' . $file;
                    if (is_dir($realPath . '/' . $file)) {
                        $dirName .= basename($file) . '-';
                        $controllerList = array_merge($controllerList, $this->readDir($newRealPath, $module, $dirName));
                        $dirName = '';
                    }
                    if (substr($file, strrpos($file, '.') - 10) === 'Controller.php') {
                        $controllerList[] = [
                            'file'    => $file,
                            'path'    => $realPath,
                            'dirPath' => trim($dirName, '-'),
                            'module'  => $module,
                        ];
                    }
                }
            }
            closedir($handle);
        }

        return $controllerList;
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
        if (isset($module->controllerAliases)) {
            foreach ($module->controllerAliases as $alias) {
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
