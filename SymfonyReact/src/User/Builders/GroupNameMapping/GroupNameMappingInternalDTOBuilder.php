<?php

namespace App\User\Builders\GroupNameMapping;

use App\Authentication\Entity\GroupNameMapping;
use App\User\DTO\Internal\GroupNameMappingDTOs\AddGroupMappingDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\User;

class GroupNameMappingInternalDTOBuilder
{
    public static function buildGroupNameMappingInternalDTO(
        User $userToAddToGroupName,
        GroupNames $groupNameObject,
    ): AddGroupMappingDTO {
        $newGroupNameMapping = new GroupNameMapping();

        $groupNameMapping = new AddGroupMappingDTO(
            $userToAddToGroupName,
            $groupNameObject,
            $newGroupNameMapping,
        );

        return $groupNameMapping;
    }
}
