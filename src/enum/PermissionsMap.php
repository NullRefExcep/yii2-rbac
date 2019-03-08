<?php

namespace nullref\rbac\enum;

use Yii;

class PermissionsMap
{
    const PERMISSION_CREATE = 'create';
    const PERMISSION_VIEW   = 'view';
    const PERMISSION_UPDATE = 'update';
    const PERMISSION_DELETE = 'delete';

    public static function getPermissions()
    {
        return [
            self::PERMISSION_CREATE => Yii::t('rbac', 'Create'),
            self::PERMISSION_VIEW   => Yii::t('rbac', 'View'),
            self::PERMISSION_UPDATE => Yii::t('rbac', 'Update'),
            self::PERMISSION_DELETE => Yii::t('rbac', 'Delete'),
        ];
    }
}