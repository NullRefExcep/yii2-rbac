Yii2 RBAC
===============

Advanced RBAC superstructure on yii2 RBAC, Dektrium extension

Set up

In modules configuration on 'rbac' section you 
- need to specify userProvider class which implements interface
```
nullref\rbac\interfaces\UserProviderInterface
```
with getUsers() method which has to return array of users with
required fields ['id', 'username']

- may to override ruleManager class which implements interface
```
nullref\rbac\interfaces\RuleManagerInterface
```
with getList() method, there is RuleManager in module
```
nullref\rbac\components\RuleManager
```

Your modules configuration. In `Module.php` add array with aliases for module controllers

Ex:
```
  public $controllerAliases = [
        '@app/modules/myModule/controllers',
        '@app/modules/myModule/controllers/admin',
  ];  
```

Each controller you want to be under RBAC has to be extended from:
```
\nullref\rbac\components\BaseController
```
or behavior() method has contain next item:
```
 'access' => [
                'class'      => \nullref\rbac\filters\AccessControl::class,
                'controller' => $this,
            ],
```