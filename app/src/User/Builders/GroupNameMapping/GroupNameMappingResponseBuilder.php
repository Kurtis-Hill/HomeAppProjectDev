<?php

namespace App\User\Builders\GroupNameMapping;

use App\Authentication\Entity\GroupMapping;
use App\User\Builders\GroupName\GroupResponseDTOBuilder;
use App\User\Builders\User\UserResponseBuilder;
use App\User\DTO\Response\GroupNameMappingDTOs\GroupNameMappingFullResponseDTO;

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
