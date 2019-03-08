<?php

namespace nullref\rbac\repositories\interfaces;

use nullref\rbac\ar\FieldAccess;
use nullref\rbac\forms\FieldAccessForm;

interface FieldAccessRepositoryInterface
{
    public function findOneWithAuthItems($id);
    
    public function findOneByMSA($model, $scenario, $attribute);
    
    public function findItems($model, $scenario, $attribute);

    public function findItemsForScenario($model, $scenario);

    public function saveWithItems(FieldAccessForm $form);

    public function updateWithItems(FieldAccessForm $form, FieldAccess $elementAccess);
}
