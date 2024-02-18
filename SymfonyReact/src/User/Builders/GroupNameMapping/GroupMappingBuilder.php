<?php

namespace App\User\Builders\GroupNameMapping;

use App\Authentication\Entity\GroupMapping;
use App\User\Entity\Group;
use App\User\Entity\User;

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
