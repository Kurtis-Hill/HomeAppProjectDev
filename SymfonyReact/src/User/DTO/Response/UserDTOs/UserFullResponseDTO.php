<?php

namespace App\User\DTO\Response\UserDTOs;

use App\User\DTO\Response\GroupDTOs\GroupResponseDTO;
use DateTimeInterface;

readonly class UserFullResponseDTO
{
    public function __construct(
        private int $userID,
        private string $firstName,
        private string $lastName,
        private string $email,
        private GroupResponseDTO $group,
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

    public function getGroup(): GroupResponseDTO
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
