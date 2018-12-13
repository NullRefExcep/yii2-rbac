<?php

namespace nullref\rbac\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class UserColumns
{
    /** @var array */
    private $users;

    public function __construct(
        array $users
    )
    {
        $this->users = $users;
    }

    public function getColumns()
    {
        if (count($this->users) == 0) {
            return [];
        }
        $userKeys = array_keys($this->users);

        $user = $this->users[$userKeys[0]];
        $columns = [];
        $keys = array_keys($user);
        foreach ($keys as $key => $attributeName) {
            $columns[] = [
                'attribute' => $attributeName,
                'label'     => Yii::t('user', lcfirst($attributeName)),
                'format'    => 'raw',
                'value'     => $attributeName,
            ];
        }

        return $columns;
    }
}