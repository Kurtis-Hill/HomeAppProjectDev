<?php

namespace App\Builders\User\GroupNameMapping;

use App\Entity\Authentication\GroupMapping;
use App\Entity\User\Group;
use App\Entity\User\User;

class GroupMappingBuilder
{
    public function buildGroupNameMapping(
        Group $groupNameObject,
        User $userObject
    ): GroupMapping {
        $groupNameMapping = new GroupMapping();
        $groupNameMapping->setGroup($groupNameObject);
        $groupNameMapping->setUser($userObject);

        return $groupNameMapping;
    }
}
