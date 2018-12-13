<?php

namespace nullref\rbac\helpers;

use Yii;

class UserFilter
{
    /** @var array */
    private $users;

    public function __construct(
        array $users
    )
    {
        $this->users = $users;
    }

    public function getUsername($id)
    {
        $username = Yii::t('rbac', 'N/A');
        foreach ($this->users as $user) {
            if (!isset($user['id'])) {
                break;
            }
            if ($user['id'] == $id) {
                $username = $user['username'];
                break;
            }
        }

        return $username;
    }

}