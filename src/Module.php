<?php

namespace nullref\rbac;

use nullref\core\interfaces\IAdminModule;
use nullref\core\interfaces\IHasMigrateNamespace;
use nullref\rbac\components\RuleManager;
use nullref\rbac\interfaces\UserProviderInterface;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\base\Module as BaseModule;
use yii\web\User as UserComponent;

/**
 * Class Module
 *
 * @package nullref\rbac
 */
class Module extends BaseModule implements IAdminModule, IHasMigrateNamespace
{
    /** @var string */
    public $elementEditorRole = 'elementEditor';

    /** @var string */
    public $loginUrl = '/user/login';

    /** @var UserProviderInterface|null */
    public $userProvider = null;

    /** @var @var string|null */
    public $userComponent = null;

    /** @var RuleManager */
    public $ruleManager;

    /** @var array */
    public $controllerAliases = [
        '@nullref/rbac/controllers',
    ];

    /** @var array */
    public $viewPathAliases = [];

    /** @var array */
    public $classMap = [];

    /** @var array */
    public $defaultClassMap = [];

    /** @var UserComponent|null */
    private $userIdentity;

    /**
     * @return UserComponent|null
     */
    public function getUserIdentity()
    {
        return $this->userIdentity;
    }

    /**
     * @param $identity
     */
    public function setUserIdentity($identity)
    {
        $this->userIdentity = $identity;
    }

    /**
     * Item for admin menu
     * @return array
     */
    public static function getAdminMenu()
    {
        return [
            'label' => Yii::t('rbac', 'Access control'),
            'icon'  => FA::_WRENCH,
            'order' => 6,
            'items' => [
                [
                    'label' => Yii::t('rbac', 'Actions access'),
                    'icon'  => FA::_MAP_SIGNS,
                    'url'   => '/rbac/access/',
                ],
                [
                    'label' => Yii::t('rbac', 'Elements access'),
                    'icon'  => FA::_EXTERNAL_LINK,
                    'url'   => '/rbac/element/',
                ],
                [
                    'label' => Yii::t('rbac', 'Assignments'),
                    'icon'  => FA::_GAVEL,
                    'url'   => '/rbac/assignment/',
                ],
                [
                    'label' => Yii::t('rbac', 'Auth items'),
                    'icon'  => FA::_GEARS,
                    'url'   => '/rbac/auth-item/',
                ],
                [
                    'label' => Yii::t('rbac', 'Roles'),
                    'icon'  => FA::_GEAR,
                    'url'   => '/rbac/role/',
                ],
                [
                    'label' => Yii::t('rbac', 'Permissions'),
                    'icon'  => FA::_GEAR,
                    'url'   => '/rbac/permission/',
                ],
                [
                    'label' => Yii::t('rbac', 'Rules'),
                    'icon'  => FA::_GAVEL,
                    'url'   => '/rbac/rule/',
                ],
            ],
        ];
    }

    /**
     * Return path to folder with migration with namespaces
     *
     * @param $defaults
     *
     * @return array
     */
    public function getMigrationNamespaces($defaults)
    {
        return ['nullref\rbac\migrations_ns'];
    }
}
