<?php

namespace App\DTOs\User\Response\GroupNameMappingDTOs;

use App\DTOs\User\Response\GroupDTOs\GroupResponseDTO;
use App\DTOs\User\Response\UserDTOs\UserResponseDTO;
use App\Services\Request\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class GroupNameMappingFullResponseDTO
{
    public function __construct(
        private int $groupMappingID,
        private UserResponseDTO $user,
        private GroupResponseDTO $group,
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getGroupMappingID(): int
    {
        return $this->groupMappingID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getUser(): UserResponseDTO
    {
        return $this->user;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getGroup(): GroupResponseDTO
    {
        return $this->group;
    }
}
