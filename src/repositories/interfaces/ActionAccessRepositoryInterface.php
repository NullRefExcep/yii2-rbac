<?php

namespace nullref\rbac\repositories\interfaces;

use nullref\rbac\ar\ActionAccess;
use nullref\rbac\forms\ActionAccessForm;

interface ActionAccessRepositoryInterface
{
    public function findOneWithAuthItems($id);

    public function findOneByMCA($module, $controller, $action);

    public function updateWithItems(ActionAccessForm $form, ActionAccess $actionAccess);
}
