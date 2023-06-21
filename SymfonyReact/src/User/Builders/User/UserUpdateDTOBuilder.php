<?php

namespace App\User\Builders\User;

use App\User\DTO\Internal\User\UserUpdateDTO;
use App\User\Entity\User;

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
