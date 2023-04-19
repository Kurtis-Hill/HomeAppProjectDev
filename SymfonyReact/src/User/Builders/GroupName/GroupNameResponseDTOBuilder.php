<?php

namespace App\User\Builders\GroupName;

use App\User\DTO\Response\GroupDTOs\GroupResponseDTO;
use App\User\Entity\GroupNames;

class GroupNameResponseDTOBuilder
{
    public static function buildGroupNameResponseDTO(GroupNames $groupName): GroupResponseDTO
    {
        return new GroupResponseDTO(
            $groupName->getGroupID(),
            $groupName->getGroupName(),
        );
    }
}
