<?php

namespace App\User\Builders\GroupName;

use App\User\Entity\GroupNames;

class GroupNameBuilder
{
    public function buildNewGroupName(string $groupName): GroupNames
    {
        $groupNames = new GroupNames();
        $groupNames->setGroupName($groupName);
        $groupNames->setCreatedAt();

        return $groupNames;
    }
}
