<?php

namespace App\Builders\User\GroupNameMapping;

use App\Builders\User\GroupName\GroupResponseDTOBuilder;
use App\Builders\User\User\UserResponseBuilder;
use App\DTOs\User\Response\GroupNameMappingDTOs\GroupNameMappingFullResponseDTO;
use App\Entity\Authentication\GroupMapping;

class GroupNameMappingResponseBuilder
{
    public static function buildGroupNameFullResponseDTO(GroupMapping $groupNameMapping): GroupNameMappingFullResponseDTO
    {
        return new GroupNameMappingFullResponseDTO(
            $groupNameMapping->getGroupMappingID(),
            UserResponseBuilder::buildUserResponseDTO($groupNameMapping->getUser()),
            GroupResponseDTOBuilder::buildGroupNameResponseDTO($groupNameMapping->getGroup()),
        );
    }
}
