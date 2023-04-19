<?php

namespace App\User\Builders\User;

use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\DTO\Response\UserDTOs\UserFullResponseDTO;
use App\User\Entity\User;

class UserResponseBuilder
{
    public static function buildFullUserResponseDTO(
        User $user,
        bool $showProfilePic = false,
        bool $showRoles = false,
    ): UserFullResponseDTO {
        return new UserFullResponseDTO(
            $user->getUserID(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($user->getGroup()),
            $user->getCreatedAt(),
            $showProfilePic !== true ? null : $user->getProfilePic(),
            $showRoles !== true ? null : $user->getRoles(),
        );
    }
}
