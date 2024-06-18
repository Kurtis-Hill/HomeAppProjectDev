<?php

namespace App\Builders\User\GroupName;

use App\DTOs\User\Response\GroupDTOs\GroupResponseDTO;
use App\Entity\User\Group;

class GroupResponseDTOBuilder
{
    public static function buildGroupNameResponseDTO(Group $groupName): GroupResponseDTO
    {
        return new GroupResponseDTO(
            $groupName->getGroupID(),
            $groupName->getGroupName(),
        );
    }
}
