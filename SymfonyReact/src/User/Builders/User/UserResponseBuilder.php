<?php

namespace App\User\Builders\User;

use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\DTO\Response\UserDTOs\UserResponseDTO;
use App\User\Entity\User;

class UserResponseBuilder
{
    public static function buildUserResponseDTO(
        User $user,
    ): UserResponseDTO {
        return new UserResponseDTO(
            $user->getUserID(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($user->getGroup()),
            $user->getCreatedAt(),
            $user->getProfilePic(),
            $user->getRoles(),
        );
    }
}
