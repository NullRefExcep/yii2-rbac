<?php

namespace nullref\rbac\services;

use nullref\rbac\repositories\ElementAccessRepository;

class ElementCheckerService
{
    /** @var ElementAccessRepository */
    private $elementAccessRepository;

    public function __construct(
        ElementAccessRepository $elementAccessRepository
    )
    {
        $this->elementAccessRepository = $elementAccessRepository;
    }

    public function isAllowed($identificator)
    {

    }
}