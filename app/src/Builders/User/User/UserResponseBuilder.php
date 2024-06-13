<?php

namespace App\Builders\User\User;

use App\Builders\User\GroupName\GroupResponseDTOBuilder;
use App\DTOs\User\Response\UserDTOs\UserResponseDTO;
use App\Entity\User\User;
use App\Voters\UserVoter;
use Symfony\Bundle\SecurityBundle\Security;

readonly class UserResponseBuilder
{
    public function __construct(private Security $security) {}

    public function buildFullUserResponseDTO(User $user): UserResponseDTO
    {
        $canEditUser = $this->security->isGranted(UserVoter::UPDATE_USER, $user);
        $canDeleteUser = $this->security->isGranted(UserVoter::DELETE_USER, $user);

        return self::buildUserResponseDTO($user, $canEditUser, $canDeleteUser);
    }

    public static function buildUserResponseDTO(
        User $user,
        ?bool $canUpdate = null,
        ?bool $canDelete = null,
    ): UserResponseDTO {
        return new UserResponseDTO(
            $user->getUserID(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
            GroupResponseDTOBuilder::buildGroupNameResponseDTO($user->getGroup()),
            $user->getCreatedAt(),
            $user->getProfilePic(),
            $user->getRoles(),
            $canUpdate,
            $canDelete,
        );
    }
}
