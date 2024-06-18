<?php

namespace App\DTOs\User\Response\UserDTOs;

use App\DTOs\User\Response\GroupDTOs\GroupResponseDTO;
use App\Services\Request\RequestTypeEnum;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class UserResponseDTO
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
        private ?bool $canEdit = null,
        private ?bool $canDelete = null,
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getUserID(): int
    {
        return $this->userID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getLastName(): string
    {
        return $this->lastName;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getEmail(): string
    {
        return $this->email;
    }

    #[
        Groups([
            RequestTypeEnum::FULL->value,

            RequestTypeEnum::SENSITIVE_FULL->value,
        ]),

    ]
    public function getGroup(): GroupResponseDTO
    {
        return $this->group;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    #[Groups([
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    #[Groups([
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCanEdit(): ?bool
    {
        return $this->canEdit;
    }

    #[Groups([
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCanDelete(): ?bool
    {
        return $this->canDelete;
    }
}
