<?php

namespace App\Builders\User\GroupName;

use App\Entity\User\Group;

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
