<?php

namespace nullref\rbac;

use Exception;
use nullref\rbac\bootstrap\Repositories;
use nullref\rbac\components\DBManager;
use nullref\rbac\components\RuleManager;
use nullref\rbac\helpers\element\ElementHtml;
use nullref\rbac\interfaces\UserProviderInterface;
use nullref\rbac\services\ElementCheckerService;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\gii\Module as GiiModule;
use yii\i18n\PhpMessageSource;
use yii\rbac\ManagerInterface;
use yii\web\Application as WebApplication;

class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     *
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        /** @var Module $module */
        if ((($module = $app->getModule('rbac')) == null) || !($module instanceof Module)) {
            return;
        };

        if ($module->userProvider === null) {
            throw new InvalidConfigException(Module::class . '::userProvider has to be set');
        }

        $module->userProvider = Yii::createObject($module->userProvider);
        $this->checkUserProvider($module);

        if ($module->userComponent === null) {
            throw new InvalidConfigException(Module::class . '::userComponent has to be set');
        }
        if ($app instanceof WebApplication) {
            $this->setUserIdentity($module);
        }

        if ($module->ruleManager === null) {
            $module->ruleManager = RuleManager::class;
        }
        $module->ruleManager = Yii::createObject($module->ruleManager);

        $classMap = array_merge($module->defaultClassMap, $module->classMap);
        //TODO
        foreach ([] as $item) {
            $className = __NAMESPACE__ . '\models\\' . $item;
            $definition = $classMap[$item];
            Yii::$container->set($className, $definition);
        }

        if ($app instanceof WebApplication) {
            if (!isset($app->i18n->translations['rbac*'])) {
                $app->i18n->translations['rbac*'] = [
                    'class'    => PhpMessageSource::class,
                    'basePath' => '@nullref/rbac/messages',
                ];
            }
        }

        if ($app->hasModule('gii')) {
            Event::on(
                GiiModule::class,
                GiiModule::EVENT_BEFORE_ACTION,
                function (Event $event) {
                    /** @var GiiModule $gii */
                    $gii = $event->sender;
                    $gii->generators['element-identifier'] = [
                        'class' => 'nullref\rbac\generators\element_identifier\Generator',
                    ];
                }
            );
        }

        if ($this->checkModuleInstalled($app)) {
            $authManager = $app->get('authManager', false);

            if (!$authManager) {
                $app->set('authManager', [
                    'class' => DBManager::class,
                ]);
            } else if (!($authManager instanceof ManagerInterface)) {
                throw new InvalidConfigException('You have wrong authManager configuration');
            }
        }

        //Repositories
        Repositories::register();

        if ($app instanceof WebApplication) {
            ElementHtml::$elementCheckerService = Yii::$container->get(ElementCheckerService::class);
        }
    }

    protected function setUserIdentity(Module $module)
    {
        $moduleUserComponent = $module->userComponent;
        try {
            $module->userComponent = Yii::$app->{$moduleUserComponent};
        } catch (Exception $e) {
            try {
                $module->userComponent = Yii::$app->getModule($moduleUserComponent);
            } catch (Exception $e) {
                throw new InvalidConfigException('Bad userComponent provided');
            }
        }

        $module->setUserIdentity($module->userComponent->identity);
    }

    /**
     * Verifies that module is installed and configured.
     *
     * @param  Application $app
     *
     * @return bool
     */
    protected function checkModuleInstalled(Application $app)
    {
        if ($app instanceof WebApplication) {
            return $app->hasModule('rbac') && $app->getModule('rbac') instanceof Module;
        } else {
            return false;
        }
    }

    /**
     * @param Module $module
     *
     * @return bool
     */
    protected function checkUserProvider(Module $module)
    {
        return $module->userProvider instanceof UserProviderInterface;
    }
}
