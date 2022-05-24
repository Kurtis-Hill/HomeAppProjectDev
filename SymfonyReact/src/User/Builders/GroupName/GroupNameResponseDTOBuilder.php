<?php

namespace App\User\Builders\GroupName;

use App\User\DTO\ResponseDTOs\GroupDTOs\GroupNameDTO;
use App\User\Entity\GroupNames;

class GroupNameResponseDTOBuilder
{
    public static function buildGroupNameResponseDTO(GroupNames $groupName): GroupNameDTO
    {
        return new GroupNameDTO(
            $groupName->getGroupNameID(),
            $groupName->getGroupNameID(),
        );
    }
}
