<?php

namespace nullref\rbac\services;

use nullref\rbac\ar\FieldAccess;

class FieldAccessService
{
    public function getItems(FieldAccess $fieldAccess)
    {
        $items = [];
        foreach ($fieldAccess->authItems as $item) {
            $items[] = $item->name;
        }

        return $items;
    }
}