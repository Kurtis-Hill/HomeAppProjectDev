<?php

namespace App\Builders\User\User;

use App\DTOs\User\Internal\User\UserUpdateDTO;
use App\Entity\User\User;

class UserUpdateDTOBuilder
{
    public static function buildUserUpdateDTO(
        User $userToUpdate,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?array $roles,
        ?string $newPassword,
        ?string $oldPassword,
        ?int $groupID,
    ): UserUpdateDTO {
        return new UserUpdateDTO(
            $userToUpdate,
            $firstName,
            $lastName,
            $email,
            $roles,
            $newPassword,
            $oldPassword,
            $groupID,
        );
    }
}
