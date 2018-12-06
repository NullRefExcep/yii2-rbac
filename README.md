Yii2 RBAC
===============

Advanced RBAC superstructure on yii2 RBAC
(inspired by Dektrium extension)

Set up

Your modules configuration. In `Module.php` add array with aliases for module controllers

Ex:
```
  public $controllerAliases = [
        '@app/modules/myModule/controllers',
        '@app/modules/myModule/controllers/admin',
  ];  
```
