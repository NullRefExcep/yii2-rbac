<?php

namespace nullref\rbac\repositories\interfaces;

interface AuthAssignmentRepositoryInterface
{
    public function getUserAssignments($userId);

    public function updateAssignments($userId, $items);
}
