<?php

namespace App\User\Builders\GroupName;

use App\User\DTO\ResponseDTOs\GroupDTOs\GroupNameResponseDTO;
use App\User\Entity\GroupNames;

class GroupNameResponseDTOBuilder
{
    public static function buildGroupNameResponseDTO(GroupNames $groupName): GroupNameResponseDTO
    {
        return new GroupNameResponseDTO(
            $groupName->getGroupNameID(),
            $groupName->getGroupNameID(),
        );
    }
}
