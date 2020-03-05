<?php

namespace nullref\rbac\bootstrap;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\ar\ActionAccessItem;
use nullref\rbac\ar\AuthAssignment;
use nullref\rbac\ar\AuthItem;
use nullref\rbac\ar\AuthItemChild;
use nullref\rbac\ar\AuthRule;
use nullref\rbac\ar\ElementAccess;
use nullref\rbac\ar\ElementAccessItem;
use nullref\rbac\ar\FieldAccess;
use nullref\rbac\ar\FieldAccessItem;
use nullref\rbac\ar\Permission;
use nullref\rbac\ar\Role;
use nullref\rbac\components\DBManager;
use nullref\rbac\repositories\ActionAccessItemRepository;
use nullref\rbac\repositories\ActionAccessRepository;
use nullref\rbac\repositories\AuthAssignmentRepository;
use nullref\rbac\repositories\AuthItemChildRepository;
use nullref\rbac\repositories\AuthItemRepository;
use nullref\rbac\repositories\cached\ActionAccessCachedRepository;
use nullref\rbac\repositories\cached\AuthAssigmentCachedRepository;
use nullref\rbac\repositories\cached\AuthItemChildCachedRepository;
use nullref\rbac\repositories\cached\ElementAccessCachedRepository;
use nullref\rbac\repositories\cached\FieldAccessCachedRepository;
use nullref\rbac\repositories\ElementAccessItemRepository;
use nullref\rbac\repositories\ElementAccessRepository;
use nullref\rbac\repositories\FieldAccessItemRepository;
use nullref\rbac\repositories\FieldAccessRepository;
use nullref\rbac\repositories\interfaces\ActionAccessRepositoryInterface;
use nullref\rbac\repositories\interfaces\AuthAssignmentRepositoryInterface;
use nullref\rbac\repositories\interfaces\AuthItemChildRepositoryInterface;
use nullref\rbac\repositories\interfaces\ElementAccessRepositoryInterface;
use nullref\rbac\repositories\interfaces\FieldAccessRepositoryInterface;
use nullref\rbac\repositories\PermissionRepository;
use nullref\rbac\repositories\RoleRepository;
use nullref\rbac\repositories\RuleRepository;
use Yii;
use yii\caching\CacheInterface;

class Repositories
{
    public static function register()
    {
        Yii::$container->set(
            ActionAccessRepositoryInterface::class,
            function ($container) {
                return new ActionAccessRepository(
                    $container->get(ActionAccessItemRepository::class),
                    ActionAccess::class
                );
            }
        );
        Yii::$container->set(
            AuthItemChildRepositoryInterface::class,
            function () {
                return new AuthItemChildRepository(AuthItemChild::class);
            }
        );
        Yii::$container->set(
            AuthAssignmentRepositoryInterface::class,
            function ($container) {
                return new AuthAssignmentRepository(
                    $container->get(DBManager::class),
                    AuthAssignment::class
                );
            }
        );
        Yii::$container->set(
            ElementAccessRepositoryInterface::class,
            function ($container) {
                return new ElementAccessRepository(
                    $container->get(ElementAccessItemRepository::class),
                    ElementAccess::class
                );
            }
        );
        Yii::$container->set(
            FieldAccessRepositoryInterface::class,
            function ($container) {
                return new FieldAccessRepository(
                    $container->get(FieldAccessItemRepository::class),
                    $container->get(FieldAccess::class)
                );
            }
        );

        //Override with cached repositories
        if (Yii::$app->cache instanceof CacheInterface) {
            Yii::$container->set(
                ActionAccessRepositoryInterface::class,
                function ($container) {
                    return new ActionAccessCachedRepository(
                        new ActionAccessRepository(
                            $container->get(ActionAccessItemRepository::class),
                            ActionAccess::class
                        )
                    );
                }
            );
            Yii::$container->set(
                AuthItemChildRepositoryInterface::class,
                function () {
                    return new AuthItemChildCachedRepository(
                        new AuthItemChildRepository(AuthItemChild::class)
                    );
                }
            );
            Yii::$container->set(
                AuthAssignmentRepositoryInterface::class,
                function ($container) {
                    return new AuthAssigmentCachedRepository(
                        new AuthAssignmentRepository(
                            $container->get(DBManager::class),
                            AuthAssignment::class
                        )
                    );
                }
            );
            Yii::$container->set(
                ElementAccessRepositoryInterface::class,
                function ($container) {
                    return new ElementAccessCachedRepository(
                        new ElementAccessRepository(
                            $container->get(ElementAccessItemRepository::class),
                            ElementAccess::class
                        )
                    );
                }
            );
            Yii::$container->set(
                FieldAccessRepositoryInterface::class,
                function ($container) {
                    return new FieldAccessCachedRepository(
                        new FieldAccessRepository(
                            $container->get(FieldAccessItemRepository::class),
                            FieldAccess::class
                        )
                    );
                }
            );
        }

        Yii::$container->set(
            ActionAccessItemRepository::class,
            function () {
                return new ActionAccessItemRepository(ActionAccessItem::class);
            }
        );
        Yii::$container->set(
            AuthItemRepository::class,
            function ($container) {
                return new AuthItemRepository(
                    AuthItem::class,
                    $container->get(DBManager::class)
                );
            }
        );
        Yii::$container->set(
            ElementAccessItemRepository::class,
            function () {
                return new ElementAccessItemRepository(ElementAccessItem::class);
            }
        );

        Yii::$container->set(
            FieldAccessItemRepository::class,
            function ($container) {
                return new FieldAccessItemRepository(
                    $container->get(FieldAccessItem::class)
                );
            }
        );
        Yii::$container->set(
            PermissionRepository::class,
            function ($container) {
                return new PermissionRepository(
                    Permission::class,
                    $container->get(AuthItemChildRepositoryInterface::class)
                );
            }
        );
        Yii::$container->set(
            RoleRepository::class,
            function ($container, $params, $config) {
                return new RoleRepository(
                    $container->get(Role::class),
                    $container->get(AuthItemChildRepositoryInterface::class)
                );
            }
        );
        Yii::$container->set(
            RuleRepository::class,
            function () {
                return new RuleRepository(AuthRule::class);
            }
        );
    }
}
