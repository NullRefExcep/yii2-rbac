<?php

namespace nullref\rbac;

use nullref\core\components\ModuleInstaller;
use Yii;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

class Installer extends ModuleInstaller
{
    public function getModuleId()
    {
        return 'rbac';
    }

    /**
     * Check if auth manager is configured
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     */
    public function init()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before run installation.');
        }
        parent::init();
    }

} 
