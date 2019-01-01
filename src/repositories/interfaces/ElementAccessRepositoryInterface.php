<?php

namespace nullref\rbac\repositories\interfaces;

use nullref\rbac\ar\ElementAccess;
use nullref\rbac\forms\ElementAccessForm;

interface ElementAccessRepositoryInterface
{
    public function findItems($identifier);

    public function updateWithItems(ElementAccessForm $form, ElementAccess $elementAccess);
}
