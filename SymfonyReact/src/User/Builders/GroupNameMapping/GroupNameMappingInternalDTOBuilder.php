<?php

namespace App\User\Builders\GroupNameMapping;

use App\Authentication\Entity\GroupMapping;
use App\User\DTO\Internal\GroupMappingDTOs\AddGroupMappingDTO;
use App\User\Entity\Group;
use App\User\Entity\User;

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
