<?php

namespace App\User\Builders\GroupNameMapping;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Entity\GroupNames;
use App\User\Entity\User;

class GroupNameMappingBuilder
{
    public function buildGroupNameMapping(
        GroupNames $groupNameObject,
        User $userObject
    ): GroupNameMapping {
        $groupNameMapping = new GroupNameMapping();
        $groupNameMapping->setGroupID($groupNameObject);
        $groupNameMapping->setUser($userObject);

        return $groupNameMapping;
    }
}
