<?php

namespace App\User\Builders\GroupName;

use App\User\Entity\Group;

class GroupNameBuilder
{
    public function buildNewGroupName(string $groupName): Group
    {
        $groupNames = new Group();
        $groupNames->setGroupName($groupName);
        $groupNames->setCreatedAt();

        return $groupNames;
    }
}
