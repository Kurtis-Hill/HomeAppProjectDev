<?php

namespace App\User\DTO\Internal\User;

use App\User\Entity\User;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UserUpdateDTO
{
    public function __construct(
    private User $userToUpdate,
    private ?string $firstName = null,
    private ?string $lastName = null,
    private ?string $email = null,
    private ?array $roles = null,
    private ?string $newPassword = null,
    private ?string $oldPassword = null,
    private ?int $groupID = null,
    ) {}

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function getGroupID(): ?int
    {
        return $this->groupID;
    }

    public function getUserToUpdate(): User
    {
        return $this->userToUpdate;
    }
}
