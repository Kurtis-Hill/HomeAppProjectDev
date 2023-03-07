<?php

namespace App\User\DTO\ResponseDTOs\UserDTOs;

use App\User\DTO\ResponseDTOs\GroupDTOs\GroupNameResponseDTO;
use DateTimeInterface;

readonly class UserFullResponseDTO
{
    public function __construct(
        private int $userID,
        private string $firstName,
        private string $lastName,
        private string $email,
        private GroupNameResponseDTO $group,
        private DateTimeInterface $createdAt,
        private ?string $profilePicture = null,
        private ?array $roles = [],
    ) {
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getGroup(): GroupNameResponseDTO
    {
        return $this->group;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }
}
