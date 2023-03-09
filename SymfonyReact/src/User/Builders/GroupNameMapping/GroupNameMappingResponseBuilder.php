<?php

namespace App\User\Builders\GroupNameMapping;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\Builders\User\UserResponseBuilder;
use App\User\DTO\ResponseDTOs\GroupNameMappingDTOs\GroupNameMappingFullResponseDTO;

class GroupNameMappingResponseBuilder
{
    public static function buildGroupNameResponseDTO(GroupNameMapping $groupNameMapping): GroupNameMappingFullResponseDTO
    {
        return new GroupNameMappingFullResponseDTO(
            $groupNameMapping->getGroupNameMappingID(),
            UserResponseBuilder::buildFullUserResponseDTO($groupNameMapping->getUser()),
            GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($groupNameMapping->getGroupName()),
        );
    }
}
