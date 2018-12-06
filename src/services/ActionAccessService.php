<?php

namespace nullref\rbac\services;

use nullref\rbac\ar\ActionAccess;

class ActionAccessService
{
    public function getItems(ActionAccess $actionAccess)
    {
        $items = [];
        foreach ($actionAccess->authItems as $item) {
            $items[] = $item->name;
        }

        return $items;
    }
}