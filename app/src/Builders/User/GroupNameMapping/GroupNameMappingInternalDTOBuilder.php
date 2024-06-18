<?php

namespace App\Builders\User\GroupNameMapping;

use App\DTOs\User\Internal\GroupMappingDTOs\AddGroupMappingDTO;
use App\Entity\Authentication\GroupMapping;
use App\Entity\User\Group;
use App\Entity\User\User;

class GroupNameMappingInternalDTOBuilder
{
    public static function buildGroupNameMappingInternalDTO(
        User $userToAddToGroupName,
        Group $groupNameObject,
    ): AddGroupMappingDTO {
        $newGroupNameMapping = new GroupMapping();

        $groupNameMapping = new AddGroupMappingDTO(
            $userToAddToGroupName,
            $groupNameObject,
            $newGroupNameMapping,
        );

        return $groupNameMapping;
    }
}
