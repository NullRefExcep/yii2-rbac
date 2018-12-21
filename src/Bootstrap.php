<?php

namespace nullref\rbac;

use Exception;
use nullref\rbac\ar\ActionAccess;
use nullref\rbac\ar\ActionAccessItem;
use nullref\rbac\ar\AuthAssignment;
use nullref\rbac\ar\AuthItem;
use nullref\rbac\ar\AuthItemChild;
use nullref\rbac\ar\AuthRule;
use nullref\rbac\ar\ElementAccess;
use nullref\rbac\ar\ElementAccessItem;
use nullref\rbac\ar\Permission;
use nullref\rbac\ar\Role;
use nullref\rbac\components\DBManager;
use nullref\rbac\components\RuleManager;
use nullref\rbac\helpers\element\ElementHtml;
use nullref\rbac\interfaces\UserProviderInterface;
use nullref\rbac\repositories\ActionAccessItemRepository;
use nullref\rbac\repositories\ActionAccessRepository;
use nullref\rbac\repositories\AuthAssignmentRepository;
use nullref\rbac\repositories\AuthItemChildRepository;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\ElementAccessItemRepository;
use nullref\rbac\repositories\ElementAccessRepository;
use nullref\rbac\repositories\PermissionRepository;
use nullref\rbac\repositories\RoleRepository;
use nullref\rbac\repositories\RuleRepository;
use nullref\rbac\services\ElementCheckerService;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\gii\Module as GiiModule;
use yii\i18n\PhpMessageSource;
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
        $module->userProvider = new $module->userProvider();
        $this->checkUserProvider($module);

        if ($module->userComponent === null) {
            throw new InvalidConfigException(Module::class . '::userComponent has to be set');
        }
        $this->setUserIdentity($module);

        if ($module->ruleManager === null) {
            $module->ruleManager = RuleManager::class;
        }
        $module->ruleManager = new $module->ruleManager();

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
        Yii::$container->set(
            ActionAccessItemRepository::class,
            function ($container, $params, $config) {
                return new ActionAccessItemRepository(
                    $container->get(ActionAccessItem::class)
                );
            }
        );
        Yii::$container->set(
            ActionAccessRepository::class,
            function ($container, $params, $config) {
                return new ActionAccessRepository(
                    $container->get(ActionAccessItemRepository::class),
                    $container->get(ActionAccess::class)
                );
            }
        );
        Yii::$container->set(
            AuthAssignmentRepository::class,
            function ($container, $params, $config) {
                return new AuthAssignmentRepository(
                    $container->get(DBManager::class),
                    $container->get(AuthAssignment::class)
                );
            }
        );
        Yii::$container->set(
            AuthItemChildRepository::class,
            function ($container, $params, $config) {
                return new AuthItemChildRepository(AuthItemChild::class);
            }
        );
        Yii::$container->set(
            AuthItemRepository::class,
            function ($container, $params, $config) {
                return new AuthItemRepository(
                    AuthItem::class,
                    $container->get(DBManager::class)
                );
            }
        );
        Yii::$container->set(
            ElementAccessItemRepository::class,
            function ($container, $params, $config) {
                return new ElementAccessItemRepository(
                    $container->get(ElementAccessItem::class)
                );
            }
        );
        Yii::$container->set(
            ElementAccessRepository::class,
            function ($container, $params, $config) {
                return new ElementAccessRepository(
                    $container->get(ElementAccessItemRepository::class),
                    $container->get(ElementAccess::class)
                );
            }
        );
        Yii::$container->set(
            RoleRepository::class,
            function ($container, $params, $config) {
                return new RoleRepository(
                    $container->get(Role::class),
                    $container->get(AuthItemChildRepository::class)
                );
            }
        );
        Yii::$container->set(
            PermissionRepository::class,
            function ($container, $params, $config) {
                return new PermissionRepository(
                    $container->get(Permission::class),
                    $container->get(AuthItemChildRepository::class)
                );
            }
        );
        Yii::$container->set(
            RuleRepository::class,
            function ($container, $params, $config) {
                return new RuleRepository($container->get(AuthRule::class));
            }
        );

        ElementHtml::$elementCheckerService = Yii::$container->get(ElementCheckerService::class);
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
