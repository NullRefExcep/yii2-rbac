<?php

namespace nullref\rbac\components;

use yii\rbac\ManagerInterface as BaseManagerInterface;

interface ManagerInterface extends BaseManagerInterface
{
    /**
     * @param  integer|null $type
     * @param  array $excludeItems
     *
     * @return mixed
     */
    public function getItems($type = null, $excludeItems = []);

    /**
     * @param  integer $userId
     *
     * @return mixed
     */
    public function getItemsByUserId($userId);

    /**
     * @param  string $name
     *
     * @return mixed
     */
    public function getItem($name);
}