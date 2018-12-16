<?php

namespace nullref\rbac\services;

use nullref\rbac\ar\ElementAccess;

class ElementAccessService
{
    public function getItems(ElementAccess $elementAccess)
    {
        $items = [];
        foreach ($elementAccess->authItems as $item) {
            $items[] = $item->name;
        }

        return $items;
    }
}