Yii2 RBAC (in development now)
===============

[![Latest Stable Version](https://poser.pugx.org/nullref/yii2-rbac/v/stable)](https://packagist.org/packages/nullref/yii2-rbac) [![Total Downloads](https://poser.pugx.org/nullref/yii2-rbac/downloads)](https://packagist.org/packages/nullref/yii2-rbac) [![Latest Unstable Version](https://poser.pugx.org/nullref/yii2-rbac/v/unstable)](https://packagist.org/packages/nullref/yii2-rbac) [![License](https://poser.pugx.org/nullref/yii2-rbac/license)](https://packagist.org/packages/nullref/yii2-rbac)

Advanced RBAC superstructure on yii2 RBAC, Dektrium extension

**Main functions:**
1. **Action access by roles and permissions**
2. **UI element access by roles and permissions**
3. **Model field access by scenarios,roles and permissions**

**Installation**

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist nullref/yii2-rbac "*"
```

or add

```json
"nullref/yii2-rbac": "*"
```

to the require section of your `composer.json` file.

**Basic set up:** 

At first you need to configure `authManager` component to use database. You need to use `\nullref\rbac\components\DBManager`.

In modules configuration on 'rbac' section you 
- need to specify id (ex: user, admin, etc) of userComponent ``\yii\web\User``
with identity field and getId() method in it

- need to specify userProvider class which implements interface
```php
nullref\rbac\interfaces\UserProviderInterface
```
with getUsers() method which has to return array of users with
required fields ['id', 'username']

- may to override ruleManager class which implements interface
```php
nullref\rbac\interfaces\RuleManagerInterface
```
with getList() method, there is RuleManager in module
```php
nullref\rbac\components\RuleManager
```

**Your modules configuration:** 
In `Module.php` add array with aliases for module controllers

Ex:
```php
  public $controllerAliases = [
        '@app/modules/myModule/controllers',
        '@app/modules/myModule/controllers/admin',
  ];  
```

Each controller you want to be under RBAC has to be extended from:
```php
\nullref\rbac\components\BaseController
```
or behavior() method has contain next item:
```php
 'access' => [
                'class'      => \nullref\rbac\filters\AccessControl::class,
                'controller' => $this,
            ],
```

**Usage of UI element access:**

To use this functionality you have to:
- use widget in base layout
```php
<?= ElementConfig::widget([]) ?>
```
- specify $elementEditorRole in RBAC module by default 'elementEditor'
- assign this role to your user
- use html helper to build you 'a' and 'button'(for now) tags.
```php
nullref\rbac\helpers\elementt\ElementHtml
```
and provide data-identifier option with unique value to $options array
```php
    <?= ElementHtml::a('a', ['somewhere', ['data-identificator' => 'a-0.11723100 1545142675']) ?>
```
You can use gii generator to generate unique values for your element (single line code only for now). 
Before use you have to specify aliases for views folders
```php
  public $viewPathAliases = [
      '@app/modules/myModule/views',
  ];  
```

**Before:**
```php
<?php

use nullref\rbac\helpers\element\ElementHtml;
use nullref\rbac\helpers\element\ElementHtml as A;

?>
<?= ElementHtml::a('a') ?>
<?= ElementHtml::a('a', Url::to(['a']), []) ?>
<?= A::a('a', ['a']) ?>
<?= ElementHtml::a('a', Url::to(['a'])) ?>
<?= ElementHtml::a('a', Url::to(['a', 'c' => 1]), ['a' => 'a']) ?>
<?= A::a('a', Url::to(['a']), ['a' => 'a']) ?>

```

**After:**
```php
<?php

use nullref\rbac\helpers\element\ElementHtml;
use nullref\rbac\helpers\element\ElementHtml as A;

?>
<?= ElementHtml::a('a', null, ['data-identificator' => 'a-0.11723100 1545142675']) ?>
<?= ElementHtml::a('a', Url::to(['a']), ['data-identificator' => 'a-0.11726200 1545142675']) ?>
<?= A::a('a', ['a'], ['data-identificator' => 'a-0.11728500 1545142675']) ?>
<?= ElementHtml::a('a', Url::to(['a']), ['data-identificator' => 'a-0.11730500 1545142675']) ?>
<?= ElementHtml::a('a', Url::to(['a', 'c' => 1]), ['a' => 'a', 'data-identificator' => 'a-0.11732500 1545142675']) ?>
<?= A::a('a', Url::to(['a']), ['a' => 'a', 'data-identificator' => 'a-0.11734500 1545142675']) ?>
```

**Usage of Model field access:**

To use this functionality you have to:
- add array with aliases for models in `Module.php`

```php
   public $modelAliases = [
          '@app/modules/myModule/models',
   ];  
```
- add behavior in your Model and **validate** your model before save

```php
public function behaviors()
{
    return [
        'checkFields' => [
            'class' => FieldCheckerBehavior::class,
        ],
    ];
}
```
- use nullref\rbac\widgets\ActiveField field in your ActiveForm

```php
 <?php $form = ActiveForm::begin([
        'fieldClass' => nullref\rbac\widgets\ActiveField::class,
 ]); ?>
```
