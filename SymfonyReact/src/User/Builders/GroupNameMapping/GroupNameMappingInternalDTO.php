<?php

namespace App\User\Builders\GroupNameMapping;

use App\Authentication\Entity\GroupNameMapping;
use App\User\DTO\InternalDTOs\GroupNameMappingDTOs\AddGroupNameMappingDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\User;

class GroupNameMappingInternalDTO
{
    public static function buildGroupNameMappingInternalDTO(
        User $userToAddToGroupName,
        GroupNames $groupNameObject,
    ): AddGroupNameMappingDTO {
        $newGroupNameMapping = new GroupNameMapping();

        $groupNameMapping = new AddGroupNameMappingDTO(
            $userToAddToGroupName,
            $groupNameObject,
            $newGroupNameMapping,
        );

        return $groupNameMapping;
    }
}
