<?php

namespace App\User\Builders\GroupName;

use App\User\DTO\Response\GroupDTOs\GroupResponseDTO;
use App\User\Entity\Group;

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
